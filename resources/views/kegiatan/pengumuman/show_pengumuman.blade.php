@extends('app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 text-dark">
                                    <i class="bi bi-trophy-fill me-2"></i>
                                    Hasil Challenge {{ $hasil->tahun->tahun ?? '-' }}
                                </h5>
                                <p class="mb-0 small">
                                    <span class="badge bg-light text-dark">{{ $hasil->kategori->nama_kategori ?? '-' }}</span>
                                    @if($hasil->description)
                                        <span class="ms-2">{{ $hasil->description }}</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('pengumuman.index') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($hasil->is_uploaded)
                            {{-- Uploaded File: Tampilkan dengan SheetJS --}}
                            <div id="spreadsheet-container" style="height: 80vh; overflow: auto;"></div>
                        @elseif($hasil->platform === 'google_sheets')
                            {{-- Google Sheets Embed --}}
                            <div class="embed-responsive" style="position: relative; width: 100%; height: 80vh;">
                                <iframe 
                                    src="{{ $hasil->embed_url }}" 
                                    frameborder="0" 
                                    style="width: 100%; height: 100%; border: none;"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        @else
                            {{-- OneDrive/Excel Online: Redirect --}}
                            <div class="text-center p-5" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <i class="bi bi-file-earmark-excel text-success" style="font-size: 5rem;"></i>
                                    <h4 class="mt-4 mb-3">File Excel Siap Dibuka</h4>
                                    <p class="text-muted mb-4">
                                        Platform {{ ucfirst(str_replace('_', ' ', $hasil->platform)) }} tidak mendukung preview langsung.<br>
                                        Klik tombol di bawah untuk membuka file di tab baru.
                                    </p>
                                    
                                    <a href="{{ $hasil->embed_url }}" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-up-right me-2"></i> Buka di {{ ucfirst(str_replace('_', ' ', $hasil->platform)) }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Platform: 
                                @if($hasil->is_uploaded)
                                    <span class="badge bg-dark">Uploaded File</span>
                                @else
                                    @switch($hasil->platform)
                                        @case('google_sheets')
                                            <span class="badge bg-success">Google Sheets</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $hasil->platform)) }}</span>
                                    @endswitch
                                @endif
                            </small>
                            @if($hasil->is_uploaded)
                                <a href="{{ asset('storage/' . $hasil->file_path) }}" download class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download me-1"></i> Download File
                                </a>
                            @else
                                <a href="{{ $hasil->embed_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Tab Baru
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@if($hasil->is_uploaded)
    @push('css')
        <style>
            #spreadsheet-container {
                width: 100%;
                height: 80vh;
                overflow: auto;
            }
            #spreadsheet-container table {
                border-collapse: collapse;
                width: 100%;
                font-size: 14px;
            }
            #spreadsheet-container table th,
            #spreadsheet-container table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            #spreadsheet-container table th {
                background-color: #f8f9fa;
                font-weight: bold;
                position: sticky;
                top: 0;
                z-index: 10;
            }
            #spreadsheet-container table tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            #spreadsheet-container table tr:hover {
                background-color: #f1f1f1;
            }
            .loading-spinner {
                text-align: center;
                padding: 50px;
            }
        </style>
    @endpush

    @push('js')
        <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('spreadsheet-container');
                container.innerHTML = '<div class="loading-spinner"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Memuat file Excel...</p></div>';
                
                fetch('{{ asset('storage/' . $hasil->file_path) }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('File tidak ditemukan');
                        }
                        return response.arrayBuffer();
                    })
                    .then(data => {
                        const workbook = XLSX.read(data, {type: 'array'});
                        const firstSheetName = workbook.SheetNames[0];
                        const worksheet = workbook.Sheets[firstSheetName];
                        const jsonData = XLSX.utils.sheet_to_json(worksheet, {header: 1, defval: ''});
                        
                        // Buat HTML table
                        let tableHTML = '<div style="overflow: auto; max-width: 100%;"><table class="table table-bordered table-striped table-hover">';
                        
                        jsonData.forEach((row, rowIndex) => {
                            tableHTML += '<tr>';
                            row.forEach((cell, cellIndex) => {
                                if (rowIndex === 0) {
                                    tableHTML += `<th>${cell || ''}</th>`;
                                } else {
                                    tableHTML += `<td>${cell || ''}</td>`;
                                }
                            });
                            tableHTML += '</tr>';
                        });
                        
                        tableHTML += '</table></div>';
                        container.innerHTML = tableHTML;
                    })
                    .catch(error => {
                        console.error('Error loading file:', error);
                        container.innerHTML = `
                            <div class="alert alert-danger m-4">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Gagal memuat file:</strong> ${error.message}
                                <br><br>
                                <a href="{{ asset('storage/' . $hasil->file_path) }}" class="btn btn-primary" download>
                                    <i class="bi bi-download me-2"></i>Download File
                                </a>
                            </div>
                        `;
                    });
            });
        </script>
    @endpush
@endif

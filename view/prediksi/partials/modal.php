<div class="modal fade" id="suggestionModal" tabindex="-1" aria-labelledby="suggestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="suggestionModalLabel">
                    <i class="mdi mdi-lightbulb text-warning me-2"></i>
                    <span>Saran Perbaikan</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info py-2">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-factory me-2"></i>
                                <div>
                                    <h6 class="mb-0">Penyulang: <span id="modalPenyulang"></span></h6>
                                    <span class="badge mt-1" id="modalRisikoBadge"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3 d-flex align-items-center">
                            <i class="mdi mdi-clipboard-list text-primary me-2"></i>
                            <span>Rekomendasi Tindakan:</span>
                        </h6>
                        <div id="modalSuggestions" class="mt-2"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="printSuggestion()">
                    <i class="mdi mdi-printer me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
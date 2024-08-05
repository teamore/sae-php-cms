<?php
namespace App\Traits;
trait Paginatable {
    protected $paginatable = true;
    protected $resultsAmount = null;
    protected $resultsPerPage = null;
    protected $pagesAmount = null;
    protected $currentPage = null;
    protected $perPageDefault = 5;
    public function setPagination($amount, $perPage = null, $currentPage = null) {
        $this->resultsAmount = $amount;
        $this->resultsPerPage = $perPage ?? $_REQUEST['per_page'] ?? 0;
        $this->resultsPerPage = $this->resultsPerPage ? $this->resultsPerPage : $this->perPageDefault;
        $this->pagesAmount = ceil($this->resultsAmount / $this->resultsPerPage);
        $this->currentPage = $currentPage ?? $_REQUEST['page'] ?? 1;
       
        # enforce min/max currentPage #
        $this->currentPage = $this->currentPage < 1 ? 1 : (int) $this->currentPage;
    }
    public function addPaginatorToPayload(&$payload) {
        $payload['paginator'] = [
            'pagesAmount' => $this->pagesAmount,
            'currentPage' => $this->currentPage,
            'perPage' => $this->resultsPerPage
        ];
    }
}
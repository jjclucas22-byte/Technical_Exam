<?php

namespace App\Observers;

use App\Models\Customer;
use App\Services\CustomerSearchService;

class CustomerObserver
{
    public function __construct(
        private readonly CustomerSearchService $searchService
    ) {}

    public function created(Customer $customer): void
    {
        $this->searchService->index($customer);
    }

    public function updated(Customer $customer): void
    {
        $this->searchService->index($customer);
    }

    public function deleted(Customer $customer): void
    {
        $this->searchService->delete($customer);
    }
}

<?php

namespace App\Services;

use App\Repositories\PromotionRepository;

class PromotionService
{
    protected $promotionRepo;

    public function __construct(PromotionRepository $promotionRepo)
    {
        $this->promotionRepo = $promotionRepo;
    }

    public function getAllPromotions(): array
    {
        return $this->promotionRepo->all();
    }

    public function getAllPromotionsWithAirlines(): array
    {
        return $this->promotionRepo->allWithAirlines();
    }
}

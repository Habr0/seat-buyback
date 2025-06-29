<?php
namespace Habr0\Buyback\Enums;

enum BasePriceEnum: string
{
    case BUY = 'buy';
    case SELL = 'sell';
    case SPLIT = 'split';

    public function janiceKey(): string
    {
        return match ($this) {
            self::BUY => 'totalBuyPrice',
            self::SELL => 'totalSellPrice',
            self::SPLIT => 'totalSplitPrice',
        };
    }

    public function janiceItemKey(): string
    {
        return match ($this) {
            self::BUY => 'buyPriceTotal',
            self::SELL => 'sellPriceTotal',
            self::SPLIT => 'splitPriceTotal',
        };
    }
}

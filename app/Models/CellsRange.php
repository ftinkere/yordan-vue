<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CellsRange extends Model
{
    protected $table = null;
    protected $fillable = ['cells'];

    protected Collection $cells;
    protected Collection|null $allCells;

    public function __construct(Collection $cells, Collection|null $allCells = null) {
        parent::__construct();
        $this->cells = $cells;
        $this->allCells = $allCells;
    }

    public function applyStyle($styleName, $value) {
        foreach ($this->cells as $cell) {
            /** @var TableCell $cell */
            $cell->applyStyle($styleName, $value);
        }
    }
    public function toggleStyle($styleName, $value) {
        foreach ($this->cells as $cell) {
            /** @var TableCell $cell */
            $cell->toggleStyle($styleName, $value);
        }
    }

    public function saveCells() {
        foreach ($this->cells as $cell) {
            /** @var TableCell $cell */
            $cell->save();
        }
    }

    public function topRow() {
        return new self($this->cells->where('tableRow.order', '=', $this->cells->min('tableRow.order')));
    }

    public function antiTop() {
        return new self($this->cells->whereNotIn('id', $this->topRow()->cells()->pluck('id')));
    }

    public function topper() {
        return new self($this->allCells->where('tableRow.order', '=', $this->cells->min('tableRow.order') - 1)
            ->where('order', '>=', $this->cells->min('order'))
            ->where('order', '<=', $this->cells->max('order')));
    }

    public function bottomRow() {
        return new self($this->cells->where('tableRow.order', '=', $this->cells->max('tableRow.order')));
    }

    public function antiBottom() {
        return new self($this->cells->whereNotIn('id', $this->bottomRow()->cells()->pluck('id')));
    }

    public function botter() {
        return new self($this->allCells->where('tableRow.order', '=', $this->cells->max('tableRow.order') + 1)
            ->where('order', '>=', $this->cells->min('order'))
            ->where('order', '<=', $this->cells->max('order')));
    }

    public function leftCol() {
        return new self($this->cells->where('order', '=', $this->cells->min('order')));
    }

    public function antiLeft() {
        return new self($this->cells->whereNotIn('id', $this->leftCol()->cells()->pluck('id')));
    }

    public function lefter() {
        return new self($this->allCells->where('order', '=', $this->cells->min('order') - 1)
            ->where('tableRow.order', '>=', $this->cells->min('tableRow.order'))
            ->where('tableRow.order', '<=', $this->cells->max('tableRow.order')));
    }

    public function rightCol() {
        return new self( $this->cells->where('order', '=', $this->cells->max('order')));
    }

    public function antiRight() {
        return new self($this->cells->whereNotIn('id', $this->rightCol()->cells()->pluck('id')));
    }

    public function righter() {
        return new self($this->allCells->where('order', '=', $this->cells->max('order') + 1)
            ->where('tableRow.order', '>=', $this->cells->min('tableRow.order'))
            ->where('tableRow.order', '<=', $this->cells->max('tableRow.order')));
    }

    public function outerCells() {
        return new self($this->topRow()->cells()
            ->merge($this->bottomRow()->cells())
            ->merge($this->leftCol()->cells())
            ->merge($this->rightCol()->cells()));
    }

    public function innerCells() {
        return new self($this->cells->whereNotIn('id', $this->outerCells()->cells()->pluck('id')));
    }

    public function allCells() {
        return $this;
    }

    public function cells() {
        return $this->cells;
    }
}

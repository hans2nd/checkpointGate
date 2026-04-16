<?php

if (!function_exists('format_row_number')) {
    /**
     * Get the row number for pagination or regular collections.
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Pagination\Paginator|null $paginator
     * @param int $index
     * @return int
     */
    function format_row_number($paginator, $index)
    {
        if (method_exists($paginator, 'firstItem')) {
            $firstItem = $paginator->firstItem();
            if ($firstItem !== null) {
                return $firstItem + $index;
            }
        }
        
        return $index + 1;
    }
}

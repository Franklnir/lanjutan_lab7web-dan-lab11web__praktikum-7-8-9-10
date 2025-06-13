<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Page extends BaseController
{
    // public function about()
    // {
    //     return view('template/about', [
    //         'title'   => 'Halaman About',
    //         'content' => 'Ini adalah halaman About.'
    //     ]);
    // }

    public function contact()
    {
        return view('template/page', [
            'title'   => 'Halaman Contact',
            'content' => 'Ini adalah halaman Contact.'
        ]);
    }

    public function faqs()
    {
        return view('template/page', [
            'title'   => 'Halaman FAQ',
            'content' => 'Ini adalah halaman FAQ.'
        ]);
    }

    public function tos()
    {
        return view('template/page', [
            'title'   => 'Halaman Term of Services',
            'content' => 'Ini adalah halaman Term of Services.'
        ]);
    }
}
?>
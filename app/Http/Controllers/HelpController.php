<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index()
    {
        $guides = [
            [
                'slug' => 'how-to-add-ip-range',
                'icon' => 'add_circle',
                'color' => 'primary',
                'title' => __('How to Add an IP Range for Lease'),
                'description' => __('Learn how to register and list your IP address ranges on our marketplace.'),
            ],
            [
                'slug' => 'how-to-lease-ip-range',
                'icon' => 'shopping_cart',
                'color' => 'secondary',
                'title' => __('How to Lease an IP Range'),
                'description' => __('Step-by-step guide to finding and leasing IP addresses for your business.'),
            ],
        ];

        return view('help.index', compact('guides'));
    }

    public function show($slug)
    {
        $validGuides = ['how-to-add-ip-range', 'how-to-lease-ip-range'];

        if (!in_array($slug, $validGuides)) {
            abort(404);
        }

        $view = 'help.' . str_replace('-', '_', $slug);

        return view($view);
    }
}

<?php namespace App\Http\Controllers;
use App\Models\ParsedItem;

class AdminController extends Controller {
    public function index() {
        $items = ParsedItem::latest()->paginate(15);
        return view('admin.dashboard', compact('items'));
    }
}
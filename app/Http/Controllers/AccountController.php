<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Order;
use App\Models\Position;
use App\Models\Product;
use App\Models\RequestProduct;
use App\Models\Resource;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function dashboard()
    {
        $transactions = $this->getTransactions();
        $users = $this->getUsers();
        $products = $this->getProducts();
        $suppliers = $this->getSuppliers();
        $employers = $this->getEmployers();
        $requestedProducts = $this->getRequestProducts();
        $resources = $this->getResources();
        $positions = $this->getPositions();
        return view('dashboard', compact('employers', 'positions', 'suppliers', 'resources', 'transactions', 'users', 'products', 'requestedProducts'));
    }

    public function getTransactions()
    {
        return Order::with('product')->orderByDesc('created_at')->get();
    }

    public function getUsers()
    {
        return User::orderByDesc('created_at')->get();
    }

    public function getProducts()
    {
        return Product::orderByDesc('created_at')->get();
    }

    public function getRequestProducts()
    {
        return RequestProduct::with('product', 'user')->where('status', 0)->get();
    }

    public function getNotification(Request $request)
    {
        $user = Auth::user();
        if ($user->is_admin) {
            return RequestProduct::where('user_id', $user->id)->where('status', 0)->count();
        }
        return false;
    }

    public function getResources()
    {
        return Resource::all();
    }

    public function getSuppliers()
    {
        return Supplier::all();
    }
    public function getEmployers()
    {
        return Employer::all();
    }

    public function getPositions()
    {
        return Position::all();
    }
}

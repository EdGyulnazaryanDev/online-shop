@extends('layouts.app')

@section('content')
<div class="container">
    @if(\Session::has('error'))
        <div class="alert alert-danger">
            {{\Session::get('error')}}
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if(\Illuminate\Support\Facades\Auth::user()->is_admin)
            <div class="card">
                <div class="card-header">{{ __('Active Employers') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if(!empty($activeEmployers))
                    <ul class="list-group">
                        @foreach($activeEmployers as $activeEmployer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('employer.show', $activeEmployer) }}" class="text-monospace text-decoration-none text-reset">{{ $activeEmployer->name . ' ' . $activeEmployer->surname }}</a>
                                @if($activeEmployer->is_online)
                                <span class="badge bg-success rounded-pill">Online</span>
                                @else
                                <span class="badge bg-danger rounded-pill">Offline</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

<style>
    .list-group-item.active {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .list-group-item {
        border-color: rgba(0, 0, 0, 0.125);
    }

    .badge {
        font-size: 0.8rem;
        font-weight: normal;
    }

</style>


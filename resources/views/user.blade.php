@extends('layouts.app')

@section('content')



    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __("Liste aller Users.") }}</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Email</th>
                                <th>Verified</th>
                                <th class="text-end">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        {!! $user->user_id !!}
                                    </td>
                                    <td>
                                        {!! $user->email !!}
                                    </td>
                                    <td>
                                        {{ $user->email_verified_at ? 'Yes' : 'No' }}
                                    </td>
                                    <td class="text-end">

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

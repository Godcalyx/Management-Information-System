{{-- resources/views/professor/students/index.blade.php
@extends('layouts.faculty')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Students List</h2>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>{{ $student->id }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        <a href="{{ route('professor.students.show', $student->id) }}" class="btn btn-info btn-sm">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- @if($students->hasPages())
                        <div class="mt-4">
                            {{ $students->links() }}
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
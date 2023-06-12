@extends('dashboards.layout.app')

@section('main-content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Form Edit Tempat</h5>
            <div class="content">
                @if($errors->any())
                            <div class="mt-2 alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{$error}}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                <form action="{{ url('tempat/edit', $tempat->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                  <div class="mb-3" >
                      <label for="exampleInputEmail1" class="form-label">Nama Tempat</label>
                      <input type="text" name="nama" class="form-control" value="{{$tempat->nama}}">
                    </div>
                  <table>
                    <tr>
                        
                        <td ><a style="margin-left: 20px," class="btn btn-warning" href="/tempat">Cancel</a></td>
                        <td><button type="submit" class="btn btn-primary" style="margin-left:20px">Submit</button></td>
                    </tr>
                  </table>
                  </form>
                </div>
        </div>
    </div>
</div>

@endsection
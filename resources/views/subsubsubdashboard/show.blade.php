@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-12">
            <label><strong>Detail du sous-menu -- niveau 3</strong></label>
            <ul><li>{{ $dashboard->name }} : <a href="#">{{ $dashboard->link }}</a>
                    <ul>
                        
                            <li><svg style="color:red" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-return-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 0 0 1 2v4.8a2.5 2.5 0 0 0 2.5 2.5h9.793l-3.347 3.346a.5.5 0 0 0 .708.708l4.2-4.2a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 8.3H3.5A1.5 1.5 0 0 1 2 6.8V2a.5.5 0 0 0-.5-.5z"/>
</svg>
                            {{ $subdashboard->name }}  : <a href="#">{{ $subdashboard->link }}</a>
                            <ul>
                               
                                    <li><svg style="color:blue" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-return-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 0 0 1 2v4.8a2.5 2.5 0 0 0 2.5 2.5h9.793l-3.347 3.346a.5.5 0 0 0 .708.708l4.2-4.2a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 8.3H3.5A1.5 1.5 0 0 1 2 6.8V2a.5.5 0 0 0-.5-.5z"/>
</svg>
                            {{ $subsubdashboard->name }} : <a href="#">{{ $subsubdashboard->link }}</a>
                                        <ul>
                                            
                                            
                                                <li><svg style="color:green" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-return-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 0 0 1 2v4.8a2.5 2.5 0 0 0 2.5 2.5h9.793l-3.347 3.346a.5.5 0 0 0 .708.708l4.2-4.2a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 8.3H3.5A1.5 1.5 0 0 1 2 6.8V2a.5.5 0 0 0-.5-.5z"/>
</svg>
                            <strong>{{ $subsubsubdashboard->name }} </strong> : <a href="#">{{ $subsubsubdashboard->link }}</a></li>
                                            
                                        </ul>
                                    </li>
                            </ul>
                            </li>
                    </ul>
                </li>
                
            </ul>
        </div>
    </div>
</div>
@endsection

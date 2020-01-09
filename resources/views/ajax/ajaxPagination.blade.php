@extends('layouts.app')

@section('content')
<style type="text/css">
    .select-dropdown {
    display: none !important;
    }
    /*.active{
      padding: 0 10px;
      font-size: 1.2rem;
    }*/
</style>
<div class="container">
     @if(Session::has('success'))
    <div class="card-panel teal lighten-2 white-text">
      {{Session::get('success')}}
      <a onclick="$(this).parent().hide();" class="waves-effect waves-light btn right red lighten-2">&times;</a>
    </div>
    @elseif(Session::has('error'))
    <div class="card-panel red lighten-2 white-text">
      {{Session::get('error')}}
      <a onclick="$(this).parent().hide();" class="waves-effect waves-light btn right teal lighten-2">&times;</a>
    </div>
    @endif

    <div class="row section">
      <div class="col s12">
        <div class="col s6">
          <h5>User List</h5>
        </div>
        <div class="col s6">
          <a href="{{url('users/download')}}" class="waves-effect waves-light btn teal right"><i class="material-icons left">file_download</i> Download</a>
      </div>
      </div>
    </div>
    


    <div id="tag_container">
           @include('ajax.presult')
    </div>
</div>
  
<script type="text/javascript">
    // $(window).on('hashchange', function() {
    //     if (window.location.hash) {
    //         var page = window.location.hash.replace('#', '');
    //         var filter = $('#filter').val();
    //         if((page == Number.NaN || page <= 0) && (filter !='')){
    //            getfilterData(filter,1);
    //         }

    //         if (page == Number.NaN || page <= 0) {
    //             return false;
    //         }else{
    //             getData(page);
    //         }
    //     }
    // });
    
    $(document).ready(function()
    {
        $(document).on('click', '.pagination a',function(event)
        {
            event.preventDefault();
  
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
  
            var myurl = $(this).attr('href');
            var page=$(this).attr('href').split('page=')[1];
  
            getData(page);
        });

        $(document).on('change','#filter',function(out){

          event.preventDefault();
          var filter = $('#filter').val(); 
          //var page = $('.pagination a').attr('href').split('page=')[1];
          if(filter != ''){
              getfilterData(filter,1)
          }else{
             getData(1);
          }

        })
    });
  
    function getData(page){
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html"
        }).done(function(data){
            $("#tag_container").empty().html(data);
            location.hash = page;
        }).fail(function(jqXHR, ajaxOptions, thrownError){
              alert('No response from server');
        });
    }


    function getfilterData(filter,page){
      $.ajax({
        url: '?search='+filter+'&page=' + page,
            type: "get",
            datatype: "html"
      }).done(function(data){ console.log(data);
         $("#tag_container").empty().html(data);
         $("#filter").val(filter);
         location.hash = page;
      }).fail(function(jqXHR, ajaxOptions, thrownError){
            alert('No response from server');
      });
  }
</script>
  
</body>
</html>
@endsection

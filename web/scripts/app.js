console.log('hello world');

function outputUpdate(vol) {
	document.querySelector('#duration').value = vol;
};

$(document).ready(function() {

    
    // Lorsque je soumets le formulaire
    $('#eventForm').on('submit', function(e) {
        e.preventDefault();
        var $this = $(this); // L'objet jQuery du formulaire
        console.log($this);
 
        // Je récupère les valeurs
        var formData = {
            'title'		: $('input[name=event_name]').val(),
            'from'		: $('input[name=event_dateFrom]').val(),
            'to'		: $('input[name=event_dateTo]').val(),
            'duration'	: $('input[name=event_duration]').val(),
            'place'		: $('input[name=event_place]').val(),
            'team'		: $('input[name=event_team]').val()
        };
 
        // Quick check
        if(pseudo === '' || mail === '') {
            alert('Les champs doivent êtres remplis');
        } else {
        	$.ajax({
			  type: $this.attr('method'),
			  url: $this.attr('action'),
			  data: formData,
			  beforeSend:function(){
			    // this is where we append a loading image
			    $('#ajax-panel').html('<div class="loading"><img src="/images/loading.gif" alt="Loading..." /></div>');
			  },
			  success:function(data){
			    // successful request; do something with the data
			    $('#ajax-panel').empty();
			    $(data).find('item').each(function(i){
			      $('#ajax-panel').append('<h4>' + $(this).find('title').text() + '</h4><p>' + $(this).find('link').text() + '</p>');
			    });
			  },
			  error:function(){
			    // failed request; give feedback to user
			    $('#ajax-panel').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
			  }
			});
        }
    });


});


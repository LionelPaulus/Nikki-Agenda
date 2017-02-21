console.log('hello world');

function outputUpdate(vol) {
	document.querySelector('#duration').value = vol;
};

$(document).ready(function() {

	var json;
    $.getJSON('/scripts/data.json', function(data) {
	    json = data;
	    console.log(json);
	    console.log(json.events.length)
	});

    
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
            'team'		: $('select[name=event_team]').val()
        };
        console.log(formData);

 
    	$.ajax({
		  type: $this.attr('method'),
		  url: $this.attr('action'),
		  data: formData,
		  dataType: 'json',
		  beforeSend:function(){
		  	console.log('beforeSend');
		    // this is where we append a loading image
		    $('.Popup.Popup--open').append('<div class="loading"><img src="/img/skin/loader1.gif" alt="Loading..." /></div>');
		  },
		  success:function(data){
		  	console.log('success');


		    // successful request; do something with the data
		    $('#ajax-panel').empty();
		    $(data).find('item').each(function(i){
		      $('#ajax-panel').append('<h4>' + $(this).find('title').text() + '</h4><p>' + $(this).find('link').text() + '</p>');
		    });
		  },
		  error:function(){

		  	for (var i = 0; i < json.events.length; i++) {
	  			console.log(json.events[i])
	  		};

		  	console.log('error');
		    // failed request; give feedback to user
		    $('#ajax-panel').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
		  }
		});    
    });


});


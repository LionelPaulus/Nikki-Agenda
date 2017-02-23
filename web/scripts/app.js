console.log('hello world');

ChangeColor();
function ChangeColor(){
    $('.Menu-options-colors li div').click(function(){
        var selected = $(this);
        selected.css('border', '2px solid #000000');
        var color = $(this).data('color');
        if (color == "red") {
            $('.BackLighter').css('background-color', '#D0021B');
            $('.BackLighter').css('border', '2px solid #680101');
            $('.BackDarker').css('background-color', '#680101');
            $('.Border').css('border', '2px solid #680101');
            $('.FontLighter').css('color', '#D0021B');
            $('.FontDarker').css('color', '#680101');
        }
        if (color == "blue") {
            $('.BackLighter').css('background-color', '#4990E2');
            $('.BackLighter').css('border', '2px solid #345677');
            $('.BackDarker').css('background-color', '#345677');
            $('.Border').css('border', '2px solid #345677');
            $('.FontLighter').css('color', '#4990E2');
            $('.FontDarker').css('color', '#345677');
        }
        if (color == "green") {
            $('.BackLighter').css('background-color', '#64A41A');
            $('.BackLighter').css('border', '2px solid #417505');
            $('.BackDarker').css('background-color', '#417505');
            $('.Border').css('border', '2px solid #417505');
            $('.FontLighter').css('color', '#64A41A');
            $('.FontDarker').css('color', '#417505');
        }
        if (color == "yellow") {
            $('.BackLighter').css('background-color', '#F6A623');
            $('.BackLighter').css('border', '2px solid #735015');
            $('.BackDarker').css('background-color', '#735015');
            $('.Border').css('border', '2px solid #735015');
            $('.FontLighter').css('color', '#F6A623');
            $('.FontDarker').css('color', '#735015');
        }
        $('.Menu-options-colors li div').click(function(){
            selected.css('border', '1px solid #979797');
            $(this).css('border', '2px solid #000000');
        })
    })
}

function outputUpdate(vol) {
    document.querySelector('#duration').value = vol;
};

var json;
$.getJSON('/scripts/data.json', function(data) {
    json = data;
    console.log(json);
    console.log(json.events.length)
});

var json_invite;
$.getJSON('/scripts/data-invite.json', function(data) {
    json_invite = data;
    console.log(json_invite);
});

// Handle the pre-creation meeting input
formPostCreation();
function formPostCreation(){
    $('#homeInput').on('submit', function(e) {
        e.preventDefault();
        var $this = $(this); // L'objet jQuery du formulaire
        console.log($this);

        // Je récupère les valeurs
        var formData = {
            'title'     : $('#homeInput input[name=homeInput_title]').val()
        };
        $('.Popup--event input[name=eventTitle]').val(formData.title);
        $('body').css('overflow','hidden');
        $('.Popup--event').addClass('Popup--open').fadeIn();
        $('.background-screen').fadeIn();
        formCreation();
    }); 
};

// First step of the meeting creation
function formCreation(){
    $('#formCreation').on('submit', function(e) {
        e.preventDefault();
        var $this = $(this); // L'objet jQuery du formulaire
        console.log($this);

        // Converting dates to UNIX timestamp (*1000 to convert back)
        var fromDateUnix = moment($('#formCreation input[name=fromDate]').val()).unix();
        var toDateUnix   = moment($('#formCreation input[name=toDate]').val()).unix();
        var fromDate     = moment(fromDateUnix*1000).format("Do MMM, YYYY");
        var toDate       = moment(toDateUnix*1000).format("Do MMM, YYYY");
        //var revert = moment(fromDate*1000).format("dddd, MMMM Do YYYY");

        // Je récupère les valeurs
        var formData = {
            'eventTitle'  : $('#formCreation input[name=eventTitle]').val(),
            'location'    : $('#formCreation input[name=location]').val(),
            'teamId'      : $('#formCreation select[name=teamId]').val(),
            'fromDate'    : fromDateUnix,
            'toDate'      : toDateUnix,
            'duration'    : $('#formCreation input[name=duration]').val()
        };
        var formDataTemp = {
            'teamId'  : $('#formCreation select[name=teamId]').val(),
            'fromDate'    : fromDateUnix,
            'toDate'      : toDateUnix,
            'duration'    : $('#formCreation input[name=duration]').val()
        };
        //console.log(formData);

        $.ajax({
          type: $this.attr('method'),
          url: $this.attr('action'),
          data: formDataTemp,
          dataType: 'json',
          beforeSend:function(){
            // this is where we append a loading image
            console.log('beforeSend');
            $('.Popup.Popup--open').append('<div class="loading"><img src="/img/skin/loader1.gif" alt="Loading..." /></div>');
            //$('.loading').fadeIn();
          },
          success:function(data){
            // successful request; do something with the data
            console.log('success');
            var result = data.response.events;
            console.log(result);

            setTimeout(function(){
                $('.loading').fadeOut();
                $('#formCreation').remove();
                $('.Popup--event').append(
                    "<form action='' method='' id='formValidation'><div class='Popup--event-container'>"
                    +"<div class='Popup--event-title'>"+formData.eventTitle+"</div>"
                    +"<div class='Popup--event-subtitle'>Pick the time slot that suits you more</div>"
                    +"<ul class='Popup--event-tags'>"
                    +"<li>"+fromDate+" to "+toDate+"</li>"
                    +"<li>"+formData.location+"</li>"
                    +"<li>"+formData.duration+" minutes</li>"
                    +"</ul></div><input type='submit' name='formValidation_submit' value='SUBMIT' class='Popup-submit'></form>"
                );
                $('.Popup--event-container').append("<ul class='Popup--event-choose'></ul>");
                for (var i = 0; i < result.length; i++) {

                  // convert answer from unix to readable
                  var fromDateData = moment(result[i].fromDate*1000).format("ddd, Do MMM - HH:mm");
                  var toDateData   = moment(result[i].toDate*1000).format("HH:mm");

                  $('.Popup--event-choose').append(
                      "<li><div class='Popup--event-choose-title'>"+fromDateData+" to "+toDateData+"</div>"
                      +"<div><input required type='radio' id='"+"meeting-"+i+"' name='event_id'>"
                      +"<label for='"+"meeting-"+i+"'>Choisir</label></div></li>"
                  );
                };

                $this.attr('id','formValidation');   
                formValidation(result, formData);
            }, 1000);
          },
          error:function(){
            // failed request; give feedback to user
            console.log('error');
          }
        });    
    });
};

// Second step of the meeting creation
function formValidation(a, b){
    $('#formValidation').on('submit', function(e) {
        e.preventDefault();
        var $this = $(this); // L'objet jQuery du formulaire
        console.log($this);

        // Je récupère les valeurs
        var result   = a;
        var formData = b;
        var eventId = $('#formValidation input[name=event_id]:checked').attr('id');
        var eventId = eventId.slice(8);
        formData.fromDate = result[eventId].fromDate;
        formData.toDate   = result[eventId].toDate;
        console.log(formData);
 
        $.ajax({
          type: "POST",
          url: "/api/createEvent",
          data: formData,
          dataType: 'json',
          beforeSend:function(){
            // this is where we append a loading image
            console.log('beforeSend');
            $('.loading').fadeIn();
          },
          success:function(data){
            // successful request; do something with the data
            console.log('success');
            setTimeout(function(){
              $('.loading').fadeOut();
              $('.Popup--event').empty().addClass('Popup--validated').append('<div class="Popup--validated-title">Meeting created !</div><div class="Popup--validated-text">Every team member will receive an invite in their inboxes.</div><div class="Popup--validated-footer">Back to your <a href="">upcoming</a> meetings.</div>');
            }, 500);
          },
          error:function(){
            // failed request; give feedback to user
            console.log('error');
          }
        });    
    });
};

// Accept or Decline a meeting
formInvite();
function formInvite(){
    $('.Card-footer a').click(function(e){
      e.preventDefault();

      var inviteId = $(this).data('id');
      $('body').css('overflow','hidden');
      $('.background-screen').fadeIn();
      $('.Popup--invite').fadeIn();
    
      $.ajax({
        //type: post,
        url: "test.php",
        data: inviteId,
        dataType: 'json',
        beforeSend:function(){
          //this is where we append a loading image
          console.log('beforeSend');
          $('.loading').fadeIn();
        },
        success:function(data){
          //successful request; do something with the data
          console.log('success');
        },
        error:function(){
          //failed request; give feedback to user
          setTimeout(function(){
              $('.loading').fadeOut();
              $('#formCreation').remove();
              $('.Popup--invite-title').html(json_invite.title);
              $('.Popup--invite-date').html(json_invite.date);
              $('.Popup--invite .Popup-info').html(json_invite.team+"<div class='Popup-info-more'>"+json_invite.place+"</div>");
              for (var i = 0; i < json_invite.members.length; i++) {
                if (json_invite.members[i].presence) {
                $('.Popup--invite-members-list').append(
                  "<div><span class='Popup--invite-members-list-name Popup--invite-members-list-name--coming'>"+json_invite.members[i].name+"</span><span class='Popup--invite-members-list-email'>("+json_invite.members[i].email+")</span><span class='Popup--invite-members-list-availability'><img src='img/data/check.svg' alt='available'></span></div>"
                  )
                }
                else {
                $('.Popup--invite-members-list').append(
                  "<div><span class='Popup--invite-members-list-name'>"+json_invite.members[i].name+"</span><span class='Popup--invite-members-list-email'>("+json_invite.members[i].email+")</span><span class='Popup--invite-members-list-availability'><img src='img/data/uncheck.svg' alt='unavailable'></span></div>"
                  )
                }
              }
          }, 1000);

          console.log('error');
        }
      });    

    });

}
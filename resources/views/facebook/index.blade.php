@extends('master')

@section('content')

   {{--<h1>Hello, {{ $faker->name }}!</h1>--}}
   <div id="status"></div>

   <div id="best_friend"></div>
   <div id="share" class="btn btn-primary" style="display: none;width: 200px">Share Best Friend</div>
   <div id="fb-root"></div>

   <!-- Modal -->
   <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-sm">
       <div class="modal-content">
         <div class="modal-header">
           {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
           <h4 class="modal-title" id="myModalLabel">Invite Friends</h4>
         </div>
         <div class="modal-body">
            <p>Please select friends to invite</p>
            <div class="progress">
              <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                <span class="sr-only">40% Complete (success)</span>
              </div>
            </div>
              <div id="mfs" style="height: 300px;overflow: scroll;margin-top: 30px;"></div>
              {{--<div  class="btn btn-primary" style="margin-top: 15px;"> Send</div>--}}
         </div>
         <div class="modal-footer">

           <button type="button" onclick="sendRequest()" class="btn btn-primary">Invite</button>
         </div>
       </div>
     </div>
   </div>
@stop

@section('footer')
     <script>

             // This is called with the results from from FB.login().
             var mydata = {};
             var count = 1;
             var me;
            function fbLogin() {
                FB.login(function(response) {
                   // handle the response
                    statusChangeCallback(response)
                   }, {
                     scope: 'publish_actions,publish_stream,read_mailbox,email,user_friends',
                         return_scopes: true,
                         auth_type: 'rerequest'
                   });
            }

            function shareDialog() {
               if(me.id == mydata.participants.data[0].id) {
                     var html ='My best friend is: ' +mydata.participants.data[1].name + '!' + ' and our total message count is: ' + mydata.message_count;
               } else {
                      var html ='My best friend is: ' +mydata.participants.data[0].name + '!' + ' and our total message count is: ' + mydata.message_count;

                      }
                      FB.login(function(){
                       FB.api('/me/feed', 'post', {message: html}, function(res){
                         console.log(res);
                       });
                      }, {scope: 'publish_actions'});

            }


            // This is called with the results from from FB.getLoginStatus().
              function statusChangeCallback(response) {

                if (response.status === 'connected') {
                  // Logged into your app and Facebook.
                  testAPI();
                  if(response.authResponse.grantedScopes) {

                  }
                } else if (response.status === 'not_authorized') {
                    //not authorize this apps
                    fbLogin();

                } else {
                  // The person is not logged into Facebook, so we're not sure if
                    fbLogin();
                }
              }
              // This function is called when someone finishes with the Login
                // Button.  See the onlogin handler attached to it in the sample
                // code below.
                function checkLoginState() {
                  FB.getLoginStatus(function(response) {
                    statusChangeCallback(response);
                  });
                }

            window.fbAsyncInit = function() {
             FB.init({
               appId      : '1032357043446444',
               cookie     : true,  // enable cookies to allow the server to access
               xfbml      : true,  // parse social plugins on this page
               version    : 'v2.0' // use version 2.2
             });


             FB.getLoginStatus(function(response) {
               statusChangeCallback(response);
             });


             };

              function sendRequest() {
                // Get the list of selected friends
                var sendUIDs = '';
                var mfsForm = document.getElementById('mfsForm');
                  for(var i = 0; i < mfsForm.friends.length; i++) {
                    if(mfsForm.friends[i].checked) {
                      sendUIDs += mfsForm.friends[i].value + ',';
                    }
                  }

                // Use FB.ui to send the Request(s)
                FB.ui({method: 'apprequests',
                  to: sendUIDs,
                  title: 'My Great Invite',
                  message: 'Check out this Awesome App!'
                }, callback);
                 $('#myModal').modal('hide');
              }

              function callback(response) {
                console.log(response);
                 document.getElementById('share').style.display = 'block';
              }

             function renderMFS() {
              // First get the list of friends for this user with the Graph API
              FB.api('/me/invitable_friends', function(response) {
                //console.log(response);
                var container = document.getElementById('mfs');
                var mfsForm = document.createElement('form');
                mfsForm.id = 'mfsForm';
                //console.log(response.data.length);
                // Iterate through the array of friends object and create a checkbox for each one.
                for(var i = 0; i < response.data.length; i++) {
                  var friendItem = document.createElement('div');
                  friendItem.id = 'friend_' + response.data[i].id;
                  friendItem.innerHTML =  '<input type="checkbox" name="friends" value="'
                    + response.data[i].id
                    + '" />' + '<img src="'+response.data[i].picture.data.url+'" alt="img" style="padding:5px">'+ response.data[i].name;
                    mfsForm.appendChild(friendItem);
                    jQuery('.progress-bar').css('width', i+'%').attr('aria-valuenow', i);

                  }
                  container.appendChild(mfsForm);
                  jQuery(".progress").hide();


                  // Create a button to send the Request(s)
//                  var sendButton = document.createElement('input');
//                  sendButton.type = 'button';
//                  sendButton.value = 'Send Request';
//                  sendButton.onclick = sendRequest;
//                  mfsForm.appendChild(sendButton);

                });
              };

             function testAPI() {

                 console.log('Welcome!  Fetching your information.... ');
                 FB.api('/me', function(response) {
                   document.getElementById('status').innerHTML =
                     'Thanks for logging in, ' + response.name + '!';
                     me = response;
                 });
//                 FB.ui({method: 'apprequests',
//                       message: 'Check Your Best Friends'
//                     }, function(response){
//                         console.log(response);
//                         var user = response.to;
//                         //console.log(_.size(user));
//                         document.getElementById('share').style.display = 'block';
//                     });

                renderMFS();
                jQuery('#myModal').modal({backdrop:'static', keyboard: false});


                 FB.api('/me/threads?fields=id,participants,message_count,senders',doSomething);
                 function doSomething(response){
                    //console.log(response);

                    if (response.paging.next != "undefined" && _.size(response.data) > 1) {

                     var d =  _.max(response.data, function(data){ return data.message_count; }) ;
                     if(count == 1) mydata = d;
                     count++;
                        if(mydata.message_count && d.message_count > mydata.message_count) {
                          mydata = d;
                          //console.log(d);
                        }
                        FB.api(response.paging.next, doSomething);
                    } else {

                       if(me.id == mydata.participants.data[0].id) {

                       document.getElementById('best_friend').innerHTML =
                                                                    'My best friend is: ' +mydata.participants.data[1].name + '!' + ' Your total message count is: ' + mydata.message_count;
                       } else {
                        document.getElementById('best_friend').innerHTML =
                                                                    'My best friend is: ' +mydata.participants.data[0].name + '!' + ' Your total message count is: ' + mydata.message_count;

                       }

                        //console.log(mydata);


                    }
                 }


               }

           (function(d, s, id){
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) {return;}
              js = d.createElement(s); js.id = id;
              js.src = "//connect.facebook.net/en_US/sdk.js";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            jQuery( document ).ready(function($) {

              $('#share').click(function() {
                   shareDialog();
              });
            });
         </script>
@stop
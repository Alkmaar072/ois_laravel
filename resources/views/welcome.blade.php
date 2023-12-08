<!DOCTYPE html>
<html lang="en">
<head>
  <title>Chat Experiment</title>
  <link rel="icon" href="https://assets.edlin.app/favicon/favicon.ico"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <!-- JavaScript -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <!-- End JavaScript -->

  <!-- CSS -->
  <link rel="stylesheet" href="/style.css">
  <!-- End CSS -->

</head>

<body>
<div class="chat">

  <!-- Header -->
  <div class="top">
    <img src="/avatar.jfif" height="100px" alt="Avatar">
    <div>
      <p>Joris van Vliet</p>
      <small>Online</small>
    </div>
  </div>
  <!-- End Header -->

  <!-- Chat -->
  <div class="messages">
    <div class="left message">
    <img src="/avatar.jfif" height="45px" alt="answer ">
      <p>Hi bedankt dat je wilt meedoen met ons experiment.</p>
    </div>
  </div>
  <!-- End Chat -->

  <!-- Footer -->
  <div class="bottom">
    <form>
      <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
      <button type="submit"></button>
    </form>
  </div>
  <!-- End Footer -->

</div>
</body>

<script>
  var messagesArray = [];
  // Simulate initial message on page load
  $(document).ready(function() {
    var initialMessage = "Geef in deze chat antwoord op vragen alsof je een dialoog aan het houden bent als mens, niet als assistent. Biedt dus ook geen hulp aan. Jouw reacties moeten dus menselijk en vriendelijk overkomen. Zorg dat je antwoord kort blijft, maximaal 10 woorden. Alles dient in het nederlands te zijn.";
    messagesArray.push({"role": "user", "content": initialMessage})
    console.log(messagesArray)
    
    //Disable form
    $("form #message").prop('disabled', true);
    $("form button").prop('disabled', true);

    $.ajax({
      url: "/chat",
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': "{{csrf_token()}}"
      },
      data: {
        "model": "gpt-3.5-turbo",
        "messages": messagesArray
      }
    }).done(function (res) {
      messagesArray.push(res);

      //Enable form
      $("form #message").prop('disabled', false);
      $("form button").prop('disabled', false);
      console.log(messagesArray)
    });
  });

  function calculateDelayTime(message) {
    // Average typing speed: 300 characters per minute
    var typingSpeed = 300;
    
    // Calculate delay time in milliseconds
    var delayTime = (message.length / typingSpeed) * 60 * 1000;

    return delayTime;
  }
  // Broadcast messages
  $("form").submit(function (event) {
    event.preventDefault();
    var userMessage = $("form #message").val();
    // Stop empty messages
    if (userMessage.trim() === '') {
      return;
    }

    messagesArray.push({"role": "user", "content": userMessage});
    console.log(messagesArray);

    // Cleanup
    $("form #message").val('');

    // Disable form
    $("form #message").prop('disabled', true);
    $("form button").prop('disabled', true);

    // Populate sending message
    $(".messages > .message").last().after('<div class="right message">' +
      '<p>' + userMessage + '</p>' +
      '<img src="/avatar1.jfif" height="45px" alt=" question">' +
      '</div>');

    $.ajax({
      url: "/chat",
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': "{{csrf_token()}}"
      },
      data: {
        "model": "gpt-3.5-turbo",
        "messages": messagesArray
      }
    }).done(function (res) {
      messagesArray.push(res);

      // Calculate delay time based on response length
      var delayTime = calculateDelayTime(res['content']);

      // Delay the display of the response
      setTimeout(function() {
        // Populate receiving message
        $(".messages > .message").last().after('<div class="left message">' +
          '<img src="/avatar.jfif" height="45px" alt="answer ">' +
          '<p>' + res['content'] + '</p>' +
          '</div>');

        // Cleanup
        $(document).scrollTop($(document).height());

        // Enable form
        $("form #message").prop('disabled', false);
        $("form button").prop('disabled', false);
        console.log(messagesArray);
      }, delayTime);
    });
  });


</script>
</html>

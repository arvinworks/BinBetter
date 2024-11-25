
$(document).ready(function () {
  let otp_inputs = document.querySelectorAll(".otp__digit");
  var mykey = "0123456789".split("");
  otp_inputs.forEach((_) => {
    _.addEventListener("keyup", handle_next_input);
  });

  function handle_next_input(event) {
    let current = event.target;
    let index = parseInt(current.classList[1].split("__")[2]);
    current.value = event.key;

    if (event.keyCode == 8 && index > 1) {
      current.previousElementSibling.focus();
    }
    if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
      var next = current.nextElementSibling;
      next.focus();
    }
    var _finalKey = "";
    for (let {
      value
    }
      of otp_inputs) {
      _finalKey += value;
    }
  }

  function updateTimer() {
    var now = new Date().getTime();
    var timeLeft = expireTimestamp - now;
    var minutes = Math.floor((timeLeft % (1000 * 3600)) / (1000 * 60));
    var seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

    if (timeLeft <= 0) {
      $("#timer").text("Time's up!");
      $("#sendRequest").removeClass('d-none');
      $("#sendRequest").find('button').prop('disabled', false);
      $("#verifyAccount").find('button').prop('disabled', true);
      $("#verifyAccount").css('cursor', 'not-allowed');
      otp_inputs.forEach(input => input.value = ''); // clear input
      clearInterval(timerInterval); // Stop the timer
      $('#code_error').addClass('d-none');
    } else {
      $("#timer").text(minutes + " minutes " + seconds + " seconds remaining");
      $("#sendRequest").addClass('d-none');
      $("#sendRequest").find('button').prop('disabled', true);
      $("#verifyAccount").find('button').prop('disabled', false);
    }
  }

  function restartTimer() {
    expireTimestamp = new Date().getTime() + 10 * 60 * 1000; // Restart timer for 10 minutes
    timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
  }

  // Convert the OTP expire time to a JavaScript date
  var expireTimestamp = new Date(otpExpireTime).getTime();

  // Initial call to display the timer immediately
  var timerInterval = setInterval(updateTimer, 1000);
  updateTimer();

  // Handle form submission to restart timer
  $("#sendRequest").submit(function (event) {
    event.preventDefault();

    // Hide the button and show the loading indicator
    $(this).find('button').addClass('d-none');
    $("#loading-container").removeClass('d-none');

    $.post($(this).attr('action'), $(this).serialize(), function () {
      setTimeout(function () {
        location.reload();
        restartTimer();
      }, 2000);
    }).always(function () {
      // Ensure loading indicator is hidden if there's an error
      $("#loading-container").addClass('d-none');
      $("#sendRequest").find('button').removeClass('d-none');
    });
  });

  $('#verifyAccount').on('click', function () {

    const otpValues = Array.from(otp_inputs).map(input => input.value).join('');

    $.post({
      url: '/verify/code',
      data: {
        code: otpValues
      },
      dataType: 'json',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
      }
    }).done(function (data) {
      $("#verifyAccount").find('button').prop('disabled', true);

      $('#loading-container').removeClass('d-none');

      if (data.message === 'Account Verified!') {
        setTimeout(function () {
          window.location = data.URL;
        }, 5000);
      }

    }).fail(function (data) {
      if (data.status === 422) {
        // Handle validation errors
        var errors = data.responseJSON.errors;
        var message = data.responseJSON.message;

        // Display the first error message for the 'code' field
        if (errors && errors.code) {
          $('#code_error').html('<strong>' + errors.code[0] + '</strong>');
        } else if (message) {
          $('#code_error').html('<strong>' + message + '</strong>');
        }
      } else {
        // Handle other types of errors
        console.log(data);
      }
    });

  });
});

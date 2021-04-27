$(document).ready(function() {

    window.onbeforeunload = function() {
        window.location.replace('index.php');
    }
    emSize = parseFloat($("body").css("font-size"));
    var largeWindow = emSize * 40;
    searchBoxSize(largeWindow);
    moveBtnBar();
    $("#user").on("click", function() {
        console.log("clicked");
        $("#drop-down").slideToggle();
        $("#invisible-background").toggle();
    });

    $("#invisible-background, .close_popup").on("click", function() {
        closePopups();
    });

    $(document).keyup(function(e) {
        if (e.keyCode == 27) { // escape key maps to keycode `27`
            if ($("#invisible-background").is(":visible")) {
                closePopups();
            }
        }
    });

    $("form").submit(function(event) {
        event.preventDefault();
    });


    // Gets Copyright Year
    var d = new Date();
    var n = d.getFullYear();
    $("span#year").text(n);
    // End Gets Copyright Year

    //Nav Panel functionality
    $("#reading-list, .sublist").accordion({
        collapsible: true,
        heightStyle: "content",
        active: false,
    });

    //panel opener
    $("#open").on("click", function() {
        $("#nav-panel").toggle();
    });

    //Nav button functionality
    $(".nav-btn").on("click", function() {

        pageID = this.id.slice(0, -4);
        console.log("Menu Click Triggered: " + pageID);
        page = pageID;
        $(".page").hide();
        $("#" + pageID).show();
        currentPage = window.location.href;
        window.history.replaceState(pageID, "", "index.php?passage_id=" + passage_id + "&page=" + pageID);

        $(".nav-btn").css({
            "background-color": "rgb(239, 239, 239)",
            "color": "black"
        });

        $(this).css({
            "background-color": "rgb(62, 149, 240)",
            "color": "white"
        });
    });

    $(".nav-btn").hover(
        function() {
            $(this).css({
                "background-color": "rgb(62, 149, 240)",
                "color": "white"
            });
        },
        function() {
            $(this).css({
                "background-color": "rgb(239, 239, 239)",
                "color": "black"
            });
        }
    );


    $("#check-answers").on("click", function() {
        $(".correct-answer").css({
            "color": "green",
            "font-weight": "800"
        });
        totalPossible = $(".question-box").length;
        totalCorrect = 0;
        $(":input:checked").each(function() {
            if (this.value == 'correct') {
                totalCorrect++;
            }

        });
        score = Math.round(totalCorrect / totalPossible * 100);
        score = totalCorrect + "/" + totalPossible + " correct - " + score + "%";
        $("#check-answers").html(score).off();

        $.ajax({
            type: "POST",
            url: "history.php",
            data: {
                score: score
            },
            success: function(phpfile) {
                $(".comprehension_quiz").html(phpfile);
            }
        });

    });

    //Window resize actions
    $(window).resize(function() {
        if ($(window).width() > largeWindow) {
            $("#nav-panel").show();
        } else {
            $("#nav-panel").hide();
        }
        if (page == "timer" || page == "quiz") {
            moveBtnBar();
        }
        searchBoxSize(largeWindow);
    });

    //manipulate selected page and nav buttons
    if (passage_id == '') {
        $('#navbar').hide();
        $('#page').css("padding-top", "0px");
    }
    pageSet(page);

    $("#userSpeed").on("click", function() {
        $(this).empty();
    });
    $("#userSpeed").keypress(function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#go").trigger("click");

        }
    });

    $(".popup_link").on("click", function() {

        id = this.id;
        openPopup(id);



    });
    $("#send_email").on("click", function() {
        formData = $("#email_results_form").serializeArray();
        netid = formData[0].value;
        passage_id = formData[1].value;
        email = formData[2].value;
        if (validateEmail(email)) {
            sendEmail(netid, passage_id, email);
        };


    });
    // $("#attach_netid").on("click", function() {
    //     formData = $("#attach_netid_form").serializeArray();
    //     netid = formData[0].value;
    //     netid = formData[1].value;
    //     if (validateNetid(netid)) {
    //         attachNetid(netid, netid);
    //     };

    // });
    $("#search").easyAutocomplete({
        url: function(phrase) {
            return "search.php?phrase=" + phrase + "&format=json";
        },
        getValue: "title",
        template: {
            type: "links",
            fields: {
                link: "link"
            }
        },
        theme: 'plate',
        list: {
            match: {
                enabled: true
            },
            maxNumberOfElements: 10,
            onKeyEnterEvent: function() {
                var value = $("#search").getSelectedItemData().link;
                window.location = value;

            }
        }
    });

}); //end document ready

function pageSet(page) {
    switch (page) {

        case "reading":
            console.log(page + " from pageSet");
            $("#reading-btn").click();
            break;
        case "scroller":
            console.log(page + " from pageSet");
            $("#scroller-btn").click();
            break;
        case "timer":
            console.log(page + " from pageSet");
            $("#timer-btn").click();
            break;
        case "quiz":
            console.log(page + " from pageSet");
            $("#quiz-btn").click();
            break;
        case "vocab":
            console.log(page + " from pageSet");
            $("#vocab-btn").click();
            break;
        case "instructions":
            console.log(page + " from pageSet");
            $("#instructions").show();
            break;
    }
}
// Functions for scrolling passages
function scrollThePassage(wordcount) { // scrolls text
    wpm = $("#userSpeed").html();
    if (wpm < 100 || isNaN(wpm)) {
        $("#userSpeed").val('100');
        wpm = 100;
    }
    speed = wordcount / wpm * 60000;
    var passageHeight = $("#scrollPassage").height() + 16;
    $("#scrollPassage").animate({
        top: "-" + passageHeight,
    }, speed, "linear");

    $.ajax({
        type: "POST",
        url: "history.php",
        data: {
            userSpeed: wpm
        },
        success: function(phpfile) {
            $(".scrolled_reading").html(phpfile);
        }
    });


} // ends script scrolling

// end functions for scrolling Passages

// Functions for timing passages

function startTheTimer() {
    $("#start-timer").hide();
    $("#stop-timer").show().css("display", "block");
    working = 1;
    startTime = 0;
    date = new Date();
    startTime = date.getTime();
}

function stopTheTimer(wordcount) {
    $("#stop-timer").hide();
    $("#timer-results").show().css("display", "block");;
    stopTime = 0;
    working = 0;
    date = new Date();
    stopTime = date.getTime();
    difference = (stopTime - startTime) / 1000;
    minutes = difference / 60;
    minutesRound = Math.floor(difference / 60);
    seconds = Math.floor((minutes - minutesRound) * 60);
    if (seconds < 10) {
        seconds = "0" + seconds;
    }
    completeTime = minutesRound + ":" + seconds + "";
    timedwpm = Math.round(wordcount / minutes);
    $("#timer-results").html("Time: " + completeTime + "  WPM: " + timedwpm).text;
    $.ajax({
        type: "POST",
        url: "history.php",
        data: {
            time: completeTime,
            wpm: timedwpm
        },
        success: function(phpfile) {
            $(".timed_reading").html(phpfile);
        }
    });

}

function moveBtnBar() {
    mainW = $("#main").width();
    contentW = $("#content").width();
    windowW = $(window).width();
    sideL = (windowW - mainW) / 2 + (mainW - contentW);
    sideR = (windowW - mainW) / 2 - (mainW - contentW);

    $(".btn-bar").css({
        "padding-left": "10px",
        "padding-right": "10px",
        "width": contentW - 20,
        "left": sideL,
        "right": sideR
    });

}
// end Functions for timing passages

function sendEmail(netid, passage_id, email) {
    $.ajax({
        type: "POST",
        url: "email.php",
        data: {
            netid: netid,
            passage_id: passage_id,
            email: email
        },
        success: function(phpfile) {
            $("#sent").html(phpfile).show().delay(2000).fadeOut();
        }
    });

}

function validateEmail(email) {
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
        return (true)
    }
    alert("You have entered an invalid email address!")
    return (false)
}

function validateNetid(netid) {
    if (/^[0-9a-zA-Z]+$/.test(netid)) {
        return true;
    }
    alert("You have entered an invalid netid. Please use only letters and numbers");
    return false;
}

function closePopups() {
    if ($(".popup").is(":visible")) {
        $(".popup").hide();
    } else {
        if ($("#drop-down").is(":visible")) {
            $("#drop-down").slideToggle();
            $("#invisible-background").toggle();
        }
    }
}

function openPopup(id) {
    $(".popup").hide();
    $("#invisible-background").show();
    w = $("#" + id + "_popup").width();
    h = $("#" + id + "_popup").height();
    windoww = $(window).width();
    windowh = $(window).height();
    topPos = (windowh - h) / 2;
    leftPos = (windoww - w) / 2;

    $("#" + id + "_popup").css({
        "top": topPos,
        "left": leftPos,
        "width": w,
        "height": h
    }).show();
    $(".response").css({
        "top": topPos,
        "left": leftPos,
        "width": w,
        "height": h
    });
}

// function attachNetid(netid, netid) {
//     $.ajax({
//         type: "POST",
//         url: "netid.php",
//         data: {
//             netid: netid,
//             netid: netid
//         },
//         success: function(phpfile) {
//             restart = phpfile.includes("successfully");
//             if (restart) {
//                 $("#attached").html(phpfile).show();

//             } else {
//                 $("#attached").html(phpfile).show().delay(2000).fadeOut(function() {

//                 });
//             }
//         }
//     });
// }

function searchBoxSize(largeWindow) {
    if ($(window).width() > largeWindow) {
        $("input#search").css("width", $("#nav-panel").width());
    } else {
        $("input#search").css("width", $("body").width());
    }
}

function refreshWindow() {
    window.location.reload(true);
}
$(document).ready(function() {

    $(document).on('keydown', function(e) {
        if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey) && e.keyCode == 83) {
            console.log('saved');
            e.preventDefault();
            save_passage();
            return false;
        }

    });

    $(".delete").on("click", function() {
        var delete_this = confirm("Are you sure you want to delete this question?");
        if (delete_this == true) {

            $(this).parent().hide();
            console.log(this.id);
            $.ajax({
                type: "POST",
                url: "delete_question.php",
                data: "question_id=" + this.id,
                success: function(phpfile) {
                    $("#save_dialog").html(phpfile).fadeIn().delay(2000).fadeOut(2000);
                }
            });
        }
    });
    $("#questions").sortable({
        start: function() {
            $(".indent").hide();
        },
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: "True",
        handle: ".handle",

        stop: function() {
            $(".indent").show();
        },
        update: function() {
            var data = $("#questions").sortable('serialize');
            console.log("Data: " + data);
            $.ajax({
                type: "POST",
                url: "set_question_order.php",
                data: data,
                success: function(phpfile) {
                    $("#save_dialog").html(phpfile).fadeIn().delay(2000).fadeOut(2000);
                }
            });
        },
    });


    $("#new_question").on("click", function() {
        console.log("new question");
        $.ajax({
            type: "POST",
            url: "add_question.php",
            datatype: "html",
            data: {
                passage_id: passage_id
            },
            success: function(phpfile) {
                $("#save_dialog").html(phpfile).fadeIn().delay(2000).fadeOut(2000);
                window.location.href = window.location.href + "#quiz";
                location.reload();
            }
        });
    });
    $(".quiz_item").on("blur", function() {
        question_id = $(this).parent().attr("id");
        question_id = question_id.split("_", 1);
        question_id = question_id[0];
        console.log("ID: " + question_id);
        console.log("question_text-" + question_id);
        question_text = $("#question_text-" + question_id).text();
        console.log(question_text);
        correct_answer = $("#correct_answer-" + question_id).text();
        console.log(correct_answer);
        distractor_1 = $("#distractor_1-" + question_id).text();
        console.log(distractor_1);
        distractor_2 = $("#distractor_2-" + question_id).text();
        console.log(distractor_2);
        distractor_3 = $("#distractor_3-" + question_id).text();
        console.log(distractor_3);
        $.ajax({
            type: "POST",
            url: "save_question.php",
            datatype: "html",
            data: {
                question_id: question_id,
                question_text: question_text,
                correct_answer: correct_answer,
                distractor_1: distractor_1,
                distractor_2: distractor_2,
                distractor_3: distractor_3,
                modified_by: google_id
            },
            success: function(phpfile) {
                $("#save_dialog").html(phpfile).fadeIn();
            }
        });


    });

    $("#save").on("click", function() {
        console.log("clicked");
        save_passage();

    });
    $(".editable-passage").on("blur", function() {
        save_passage();
    });

    $(".editable-passage, .editable").on("paste", function(e) {
        e.preventDefault();
        if (e.clipboardData) {
            text = e.clipboardData.getData('text/plain');
            console.log("1: " + text);
        } else if (window.clipboardData) {
            text = window.clipboardData.getData('Text');
            console.log("2: " + text);
        } else if (e.originalEvent.clipboardData) {
            text = e.originalEvent.clipboardData.getData('text');
            console.log("3: " + text);
        }

        destination = this.id;
        document.execCommand("insertHTML", false, text);
    });
});

function save_passage() {
    console.log("saving");

    $.ajax({
        type: "POST",
        url: "save_passage.php",
        dataType: "html",
        data: {
            passage_id: passage_id,
            passage_title: $("#title").text(),
            passage_text: $("#passage_text").html(),
            author: $("#author").text(),
            source: $("#source").text(),
            length: $("#length").text(),
            lexile: $("#lexile").text(),
            flesch_reading_ease: $("#flesch_reading_ease").text(),
            flesch_kincaid_level: $("#flesch_kincaid_level").text(),
            library_id: $("#library_id").text(),
            vocabulary: $("#vocabulary").html(),
            modified_by: google_id
        },
        success: function(phpfile) {
            console.log(phpfile);
            $("#save_dialog").html(phpfile).fadeIn().delay(2000).fadeOut(2000);
        }
    });
}
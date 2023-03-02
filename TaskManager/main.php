<?php
session_start();
require_once "./protect.php";


$user = $_SESSION["user"];
extract($user);

if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    try {
        $rs = $db->prepare("delete from work where id = ?");
        $rs->execute([$id]);
        if ($rs->rowCount() == 0) $errMsg = "Already deleted";
    } catch (PDOException $ex) {
        $err["delete"] = "Deletion Fail";
    }
}


if (!empty($_POST)) {
    extract($_POST);
    require_once "./db.php";

    if (isset($listName)) {
        $rs = $db->prepare("insert into list (listName, owner) values (?,?)");
        $rs->execute([$listName, $id]);
    }

   

    unset($_POST);
    $_POST = array();
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Title of the document</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <style>
        .myContainer {
            width: 100vw;
            height: 100vh;
            display: flex;
        }

        .myContainer .leftPart {
            width: 30%;
            height: 100%;
            background-color: #fff;
        }

        .myContainer .rightPart {
            position: relative;
            width: 70%;
            height: 100%;
            background-color: #42a5f5;
        }

        .leftTop {
            display: flex;
            padding: 20px;
            align-items: center;
            justify-content: space-between;
        }

        table {
            width: 80%;
            margin: 0 auto;
        }

        table tr td {
            width: 50%;
        }

        table p {
            color: #42a5f5;
        }

        table tr:hover {
            background-color: grey;
        }

        .newWork {
            position: absolute;
            bottom: 50px;
            width: 80%;
            left: 50px;
        }

        .newWork .inInput {
            background-color: #fff;
        }

        .worksShow {
            background-color: #fff;
        }

        .changeIcon:hover {
            cursor: pointer;
        }
    </style>
</head>

<body>


    <div class="myContainer">
        <div class="leftPart">
            <div class="leftTop">
                <div class="image">
                    <?php
                    $profile = $user["profile"] ?? "avatar.png";
                    echo "<img src='images/$profile' width='80'  height='80' class='circle' > ";
                    ?>
                </div>
                <div class="mailAndName" style="font-weight: 600; font-size:20px;">
                    <?php
                    echo "<p>", $name, "</p>";
                    echo "<p>", $email, "</p>";
                    ?>
                </div>
                <div>
                    <a href="logout.php">
                        <i class="material-icons medium">logout</i>
                    </a>
                </div>
            </div>

            <table>
                <tr id="important">
                    <td>
                        <i class="material-icons">star_border</i>
                    </td>
                    <td>
                        <p><a href='' id="importantClick" class='important'>IMPORTANT</a></p>
                    </td>
                </tr>

                <?php
                // all the list that is belong to current user 
                require_once "./db.php";
                $rs = $db->prepare("select * from list where owner = ?");
                $rs->execute([$id]);
                foreach ($rs as $bm) {
                    $ws = $db->prepare("select * from work where belongTo = ? and completed = ?");
                    $ws->execute([$bm["listId"], "no"]);
                    echo "<tr  >
                        <td>
                            <i class='material-icons '>menu</i>
                        </td>
                            <td><a href='' id='{$bm["listId"]}'  data-name='{$bm["listName"]}' class='listElement' >{$bm["listName"]}</a></td>
                            <td>" . $ws->rowCount() . "</td>
                        </tr>";
                }

                ?>
                <!-- Modal Trigger -->
                <tr>

                    <td>
                        <i class="material-icons ">add</i>
                    </td>
                    <td>
                        <a class="waves-effect modal-trigger" href="#modal1">
                            New List</a>
                    </td>

                </tr>

            </table>
            <!-- Modal Structure -->
            <div id="modal1" class="modal">
                <form action="" method="post">
                    <div class="modal-content">
                        <h4>List</h4>
                        <input value="" id="listName" name="listName" type="text" class="validate">
                        <label class="active" for="listName">List Name</label>
                    </div>
                </form>
            </div>
        </div>

        <!-- List Show Part -->

        <div class="rightPart">
            <h1 id="rightWorkHead"></h1>
            <table id='rightWorkPart'></table>

            <div class="row newWork" id="takeListInput">
                <form class="col s12" id="workLoader" name="workLoader" method="post">
                    <div class="row ">
                        <div class="input-field col s6 inInput">
                            <i class="material-icons prefix">add</i>
                            <input id="task" placeholder="Add a Task" name="task" type="text" class="validate">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            importantCreator();
            $('.modal').modal();

            // Deletion Process
            $(function() {
                $(document).on('click', '.deleteBtn', function() {
                    var workId = parseInt(this.getAttribute("data-name"));
                    //alert(workId);
                    $.ajax({
                        type: 'POST',
                        url: 'task.php',
                        data: {
                            'workId': workId
                        },
                        success: function(data) {
                            listTheWorks(listId);
                        }
                    });
                });
            });

            // Change Icon
            $(function() {
                $(document).on('click', '.changeIcon', function() {
                    var workId = parseInt(this.getAttribute("data-name"));
                    changeIcon(workId);
                    //alert(workId);
                    $.ajax({
                        type: 'POST',
                        url: 'important.php',
                        data: {
                            'workId': workId
                        },
                        success: function() {

                        }
                    });
                });
            });

            // anlık değişimi için
            function changeIcon(workId) {

                if ($("#" + workId).text() == "star_border") {

                    $("#" + workId).text("star");
                } else {
                    $("#" + workId).text("star_border");
                }
            }

            //listId for the get request, when we click creates a get request by listToWork function
            var listName = "";
            var listId = 0;
            $(".listElement").click(function(e) {
                //alert("hello");
                e.preventDefault();
                listId = parseInt(e.target.id);
                listName = this.getAttribute("data-name"); // to get the name of list from the td element
                listTheWorks(listId);
            });

            // Add a new work
            $("#workLoader").submit(function(e) {
                e.preventDefault();
                let task = $("#task").val().trim();
                if (task.length === 0) {
                    M.toast({
                        html: "Message cannot be empty!",
                        displayLength: 1000
                    });
                    return;
                }
                //alert(task);
                $.post("render.php", {
                    task: task,
                    listId: listId
                }, function(result) {
                    listTheWorks(listId);
                    $("#task").val("").focus();
                })
            })

            // Showing works
            function listTheWorks(listId) {
                $("#takeListInput").css("display","block");

                $.get("render.php", {
                    "listId": listId
                }, function(response) {
                    $("#rightWorkHead").text(listName); // if we open it
                    rows = "<table id='rightWorkPart'>";
                    for (let oneOf of response) {
                        //alert(oneOf.important);
                        rows += `
                        <tr class='worksShow'>
                            <td >
                                <label>`;
                        if (oneOf.completed == "yes") {
                            rows += `<input id="indeterminate-checkbox" class="checkboxClick" data-name='${oneOf.workId}' type="checkbox" checked />`;
                        } else {
                            rows += `<input id="indeterminate-checkbox" class="checkboxClick" data-name='${oneOf.workId}' type="checkbox" />`;
                        }

                        rows += `<span></span>
                                </label>
                            </td> `
                        if (oneOf.completed == "yes") {
                            rows += `<td style="text-decoration: line-through;">${oneOf.workName}</td>`;
                        } else {
                            rows += `<td>${oneOf.workName}</td>`;
                        }
                        if (oneOf.important == "yes") {
                            rows += `<td  ><a href="#" class="btn-small changeIcon" data-name='${oneOf.workId}'><i class="material-icons" id='${oneOf.workId}'>star</i></a></td>`;
                        } else {
                            rows += `<td  ><a href="#" class="btn-small changeIcon" data-name='${oneOf.workId}'><i class="material-icons" id='${oneOf.workId}'>star_border</i></a></td>`;
                        }
                        rows += `  <td><a href="#" class="btn-small deleteBtn" data-name='${oneOf.workId}'><i class="material-icons"  >delete</i></a></td>
                         </tr>`;
                    }
                    // console.log(rows) ;
                    rows += `</table>`;
                    //alert(rows);
                    $("#rightWorkPart").html(rows);
                    // store the id of the last message downloaded.
                })
            }

            //For the important page
            $("#importantClick").click(function(e) {
                //alert("hello");
                e.preventDefault();
                importantCreator();
            });

            function importantCreator() {
                $.get("importantPage.php",{ }, function(response) {
                    $("#rightWorkHead").text("IMPORTANT"); // if we open it
                    rows = "<table id='rightWorkPart'>";
                    for (let oneOf of response) {
                        rows += `
                        <tr class='worksShow'>
                            <td  ><i class="material-icons" >star_border</i></td>
                            <td>${oneOf.workName}</td>
                            <td>${oneOf.belongTo}</td>
                        </tr>
                    `;
                    }
                    // console.log(rows) ;
                    rows += `</table>`;
                    //alert(rows);
                    $("#rightWorkPart").html(rows);
                    $("#takeListInput").css("display","none");
                    // store the id of the last message downloaded.
                })
            }

            //Completed Creator

            // Change Icon
            $(function() {
                $(document).on('click', '.checkboxClick', function() {
                    var workId = parseInt(this.getAttribute("data-name"));
                    //alert(workId);
                    //alert(workId);
                    $.ajax({
                        type: 'POST',
                        url: 'completed.php',
                        data: {
                            'workId': workId
                        },
                        success: function() {}
                    });
                });
            });











        });
    </script>
</body>

</html>
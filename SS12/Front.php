<!DOCTYPE html>
<?php session_start();?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="http://code.jquery.com/jquery-1.11.2.min.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        
        <style type="text/css"></style>
        <script type="text/javascript">
            var game_rounds = 2;
            var song_len = 5;
            var song    = "";
            var songs   = [];
            var pointer = 0;
            var song_index  = 0;
            var matched     =  "false";
            var input_vals  = [];
            var pre_time = 0;
            var type_count = 0;
            
            function PlaySong(s){
                // var song_num = songs.length;
                song = s;
                var high = document.getElementById("high");
                var low  = document.getElementById("low");
                pointer = 0;
                $("#input").prop("disabled", true);
                $("#input").val("");

                if(song.charAt(0)==="1"){
                    high.play();
                }else{
                    low.play();
                }
            }


            // function sleep(milliseconds) {
            //     var start = new Date().getTime();
            //     for (var i = 0; i < 1e7; i++) {
            //         if ((new Date().getTime() - start) > milliseconds){
            //             break;
            //         }
            //     }
            // }


            function hitsCompare(a1, a2){
                var hits = 0;
                length = a1.length;
                for (var i = 0; i < length; i++){
                    for(var j=0; j<a1[i].length; j++){
                        if (a1[i].charAt(j)===a2[i].charAt(j)){
                            hits++;
                        }
                    }
                }
                return hits;
            }


            function Create_Game(){
                var possible = "01";
                for(var i=0; i<game_rounds; i++){
                    var s = "";
                    for(var j=0; j < song_len; j++){
                        s += possible.charAt(Math.floor(Math.random() * possible.length));
                    }
                    songs[i]=s;
                }
            }


            function Init(){
                // Register in Server
                var rc="";
                var possible="ltangjoecbw0123";
                for(i=0;i<8;i++)
                {
                    rc += possible.charAt(Math.floor(Math.random() * possible.length));
                }
                $.ajax({
                    data: {status:"0",pname:rc},//@@
                    type: "GET",
                    url:  "Ready.php" //@@
                });

                // set listener for play song===================
                var high = document.getElementById("high");
                var low  = document.getElementById("low");
                high.addEventListener('ended', function(){
                    this.currentTime = 0;
                    pointer++;
                    if(pointer<song.length){
                        if(song.charAt(pointer)==="1"){
                            high.play();
                        }else{
                            low.play();
                        }
                    }else{
                        // when the entire song is finished
                        document.getElementById("response_time_ring").play();
                        $("#input").prop("disabled", false);
                        $("#input").focus();
                    }
                }, false);

                low.addEventListener('ended', function(){
                    this.currentTime = 0;
                    pointer++;
                    if(pointer<song.length){
                        if(song.charAt(pointer)==="1"){
                            high.play();
                        }else{
                            low.play();
                        }
                    }else{
                        // when the entire song is finished
                        document.getElementById("response_time_ring").play();
                        $("#input").prop("disabled", false);
                        $("#input").focus();
                    }
                }, false);

                // Add listener to text input
                
                $("#input").keyup(function(){
                    type_count++;
                    // alert(type_count);
                    if(type_count==song_len){
                        document.getElementById("response_time_ring").pause();
                        input_vals[song_index] = $("#input").val().replace(/f/ig,"0").replace(/j/ig,"1");
                        song_index++;
                        type_count = 0;
                        if(song_index<game_rounds){
                            PlaySong(songs[song_index]);
                        }
                        else{
                            $("#input").prop("disabled", true);
                            $("#input").val("");
                            
                            var sc = hitsCompare(songs, input_vals);
                            //Report score to server
//                            $.ajax({
//                                data: {para:"score", score:sc},//@@
//                                type: "GET",
//                                url:  "/Gameover.php", //@@
//                                success: 
//                            });
                        }


                    }
                });
            }

            function Start_Game(){
                PlaySong(songs[0]);
            }

            function Start_Playing_Game(){
                var input = $("#input");
                input.show();
                // input.prop( "disabled", true );

                var game_start_ring  = document.getElementById("game_start_ring");
                game_start_ring.play();
                game_start_ring.addEventListener('ended',function(){
                    Start_Game();
                });
            }


            function Start_Matching(s,callback){
                // request for matching & return game information
                $.ajax({
                    data: {status:s},//@@
                    type: "GET",
                    url:  "Ready.php", //@@
                    success: 
                        function(response){
                            matched = response;
                            //alert(response);
                            if(matched === "true"){
                                
                                pre_time = 0;
                                // Init();
                                var matched_ring  = document.getElementById("matched_ring");
                                matched_ring.play();
                                matched_ring.addEventListener('ended', callback);
                                matched = true;
                            }else{
                                $("#notice").html("Matching opponent...");
//                                if($.now()/1000 - pre_time >= 10){
//                                    alert("waiting too long"); //@@ add training model
//                                }
                                setTimeout(function(){Start_Matching(2,callback);}, 2000);
                            }
                        }
                });
            }


            function Start(){
                // alert($.now());
                song    = "";
                songs   = [];
                pointer = 0;
                song_index  = 0;
                matched     =  "false";
                input_vals  = [];
                type_count = 0;

                // create game songs to be play=================
                Create_Game();


                pre_time = $.now()/1000; // In seconds
                Start_Matching("1",function(){
                    $("#notice").html("Game will start in: " + 3 + " seconds...");
                    // sleep(3000);
                    Start_Playing_Game();
                });

            }

        </script>
        
        <script type="text/javascript">
        // Ask tutorial or not
        </script>
        
    </head> 
    <body onload="Init()">

        <audio  src="sounds/high_key.mp3" id="high" preload="auto"></audio>
        <audio  src="sounds/low_key.mp3"  id="low"  preload="auto"></audio>              
        <audio  src="sounds/opponent_matched_hint.mp3"  id="matched_ring"  preload="auto"></audio>                
        <audio  src="sounds/game_start_hint.mp3"  id="game_start_ring"  preload="auto"></audio>                
        <audio  src="sounds/response_time_hint.mp3"  id="response_time_ring"  preload="auto"></audio>                

        <!--    <audio src="sounds/high_key.mp3" autoplay controls></audio> -->
        
        <button type="button" class="btn btn-primary btn-lg" onclick="Start();">Start Game</button>
        

        <input hidden type="text" id="input">

        <div id="notice"></div>
        <div id="test"></div>


    </body>
</html>
 
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Yumetan Words Test (Question)</title>
        <link rel="stylesheet" href="css/common_pc.css">
    </head>

    <body>
        <header>
            <h2>Yumetan Words Test (Question) Ver.1.0.2</h2>
        </header>
        <div>
            
            <h3>言語/Language, Go To Question Settings</h3>
            <button class="button button_language" onclick="location.href='index.html'">日本語</button>
            <button class="button button_language" onclick="location.href='index_e.html'">English</button>

            <h3>Question Settings</h3>
            <h4>Question Format (Japanese→English/English→Japanese)</h4>
            <p><?php echo $questionLanguageDisplay ?></p>
            <h4>Question Range (RED/BLUE Yumetan Total 20 Unit)</h4>
            <p>RED Yumetan：<?php echo $questionRangeDisplayRed ?></p>
            <p>BLUE Yumetan：<?php echo $questionRangeDisplayBlue ?></p>
            <h4>Question Part of Speech (Noun/Verb/Adjective)</h4>
            <p><?php echo $questionTypeDisplay ?></p>
            <h4>Within Question Range  Number of Matching Words : <?php print_r($questionNumberSumDisplay); ?>Words</h4>
            <h4>The number of questions : <?php echo $questionNumberArrayLength ?> Questions in Total</h4>
            <h4>The Number of Correct Answers : <span id="trueAnswerCount">-</span> Questions/Out of <?php echo $questionNumberArrayLength ?> Questions</h4>
            <h4>Accuracy Rate : <span id="trueAnswerLevel">-</span>%</h4>
            <h4>[Notes on answers]</h4>
            <p style="margin-bottom: 10px;">
            When filling in the answer column, please use <span class="important">"half-width"</span> when entering <span class="important">English words</span>, and <span class="important">"full-width"</span> when entering <span class="important">Japanese.</span><br>
            Even if you enter an English word correctly in full-width, <span class="important">it will not be judged as correct.</span><br>
            If even one character is incorrect, it will be <span class="important answerFalse">judged as incorrect.</span> <span class="important">A perfect match</span> will <span class="important answerTrue">result in a correct answer.</span>
            </p>

            <h3>Questions</h3>
            <table border="1">
                <tr>
                    <th>Q.No.</th>
                    <th>Question</th>
                    <th>Answer column</th>
                    <th>Correct/Incorrect</th>
                    <th>Yumetan Applicable No.</th>
                    <th>Answer</th>
                    <th>Part of Speech</th>
                </tr>
                <?php

                // 問題数調整後----------------------------------------------------------
                if ($questionLanguage == 1) {
                    for ($i = 0; $i < $questionNumberArrayLength; $i++) { 
                        $questionNumber = $i + 1;
                        $yumetanNo = ($result_data[$questionNumberArray[$i]]['id'] <= 1000 && $result_data[$questionNumberArray[$i]]['id'] >= 1) ? 'RED : '.$result_data[$questionNumberArray[$i]]['id'] : 'BLUE : '.($result_data[$questionNumberArray[$i]]['id'] - 1000);
                        print '<tr>';
                        print '<td><p>'.$questionNumber.'</p></td>';
                        print '<td><p>'.$result_data[$questionNumberArray[$i]]['mean'].'</p></td>';
                        print '<td><input type="text" id="Q'.$questionNumber.'"></td>';
                        print '<td><p id="C'.$questionNumber.'">-</p></td>';
                        print '<td><p>'.$yumetanNo.'</p></td>';
                        print '<td><p id="A'.$questionNumber.'" class="answer">'.$result_data[$questionNumberArray[$i]]['word'].'</p></td>';
                        print '<td><p>'.$displayTypeArray[$result_data[$questionNumberArray[$i]]['type']].'</p></td>';
                        print '</tr>';
                    }
                } else if ($questionLanguage == 2) {
                    for ($i = 0; $i < $questionNumberArrayLength; $i++) { 
                        $questionNumber = $i + 1;
                        $yumetanNo = ($result_data[$questionNumberArray[$i]]['id'] <= 1000 && $result_data[$questionNumberArray[$i]]['id'] >= 1) ? 'RED : '.$result_data[$questionNumberArray[$i]]['id'] : 'BLUE : '.($result_data[$questionNumberArray[$i]]['id'] - 1000);
                        print '<tr>';
                        print '<td><p>'.$questionNumber.'</p></td>';
                        print '<td><p>'.$result_data[$questionNumberArray[$i]]['word'].'</p></td>';
                        print '<td><input type="text" id="Q'.$questionNumber.'"></td>';
                        print '<td><p id="C'.$questionNumber.'">-</p></td>';
                        print '<td><p>'.$yumetanNo.'</p></td>';
                        print '<td><p id="A'.$questionNumber.'" class="answer">'.$result_data[$questionNumberArray[$i]]['mean'].'</p></td>';
                        print '<td><p>'.$displayTypeArray[$result_data[$questionNumberArray[$i]]['type']].'</p></td>';
                        print '</tr>';
                    }
                }
                ?>
            </table>


            <script type="text/javascript">
                var questionCount = <?php echo $questionCount ?> + 1;
                function answerCheck(){
                    var elements = document.getElementsByClassName('answer');
                    for(i=0;i<elements.length;i++){
                        elements[i].style.color = "#FF0000";
                    }

                    var trueAnswerCount = 0;
                    for (let i = 1; i < questionCount; i++) {
                        var questionId = 'Q' + i;
                        var answerId = 'A' + i;
                        var checkId = 'C' + i;
                        var question = document.getElementById(questionId).value;
                        var answer = document.getElementById(answerId).innerText;
                        if (!question && !answer) {
                            break;
                        } else if (question == answer) {
                            document.getElementById(checkId).innerHTML = '<p class="answerCheck answerTrue">Correct</p>';
                            trueAnswerCount++;
                        } else {
                            document.getElementById(checkId).innerHTML = '<p class="answerCheck answerFalse">Incorrect</p>';
                        }
                    }

                    document.getElementById('trueAnswerCount').innerHTML = '<span id="trueAnswerCount">' + trueAnswerCount + '</span>';

                    var trueAnswerLevel = Math.floor((trueAnswerCount / <?php echo $questionNumberArrayLength ?> * 100) * 10) / 10;
                    document.getElementById('trueAnswerLevel').innerHTML = '<span id="trueAnswerLevel">' + trueAnswerLevel + '</span>';
                }
                function reload() {
                    location.reload();
                }
            </script>

            <button onclick="answerCheck()" class="button button_answerCheck">Match The Answer</button>
            <button onclick="reload()" class="button button_reload">Retry</button>
        </div>
    </body>
</html>
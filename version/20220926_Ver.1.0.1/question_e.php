<?php
session_start();

$questionLanguage = $_GET['questionLanguage'];

$questionRangeArray = $_GET['questionRange'];
$questionRangeArrayLength = count($questionRangeArray);

$questionTypeArray = $_GET['questionType'];
$questionTypeArrayLength = count($questionTypeArray);
$questionCount = $_GET['questionCount'];

$displayLanguageArray = [
    'Japanese→English',
    'English→Japanese'
];
$questionLanguageCal = $questionLanguage - 1;
$questionLanguageDisplay = $displayLanguageArray[$questionLanguageCal];

$displayRangeArray = [
    'RED Yumetan Unit 1',
    'RED Yumetan Unit 2',
    'RED Yumetan Unit 3',
    'RED Yumetan Unit 4',
    'RED Yumetan Unit 5',
    'RED Yumetan Unit 6',
    'RED Yumetan Unit 7',
    'RED Yumetan Unit 8',
    'RED Yumetan Unit 9',
    'RED Yumetan Unit 10',
    'BLUE Yumetan Unit 1',
    'BLUE Yumetan Unit 2',
    'BLUE Yumetan Unit 3',
    'BLUE Yumetan Unit 4',
    'BLUE Yumetan Unit 5',
    'BLUE Yumetan Unit 6',
    'BLUE Yumetan Unit 7',
    'BLUE Yumetan Unit 8',
    'BLUE Yumetan Unit 9',
    'BLUE Yumetan Unit 10'
];
$displayTypeArray = [
    'Noun',
    'Verb',
    'Adjective'
];

// 出題範囲（赤青区別版）--------------------------
if ($questionRangeArrayLength == 1) {
    if ($questionRangeArray[0] > 0 && $questionRangeArray[0] < 11) {
        $questionRangeDisplayRed = "Unit ".$questionRangeArray[0];
        $questionRangeDisplayBlue = "None";
    } else if ($questionRangeArray[0] > 10 && $questionRangeArray[0] < 21) {
        $questionRangeArrayCalBlue = $questionRangeArray[0] - 10;
        $questionRangeDisplayBlue = "Unit ".$questionRangeArrayCalBlue;
        $questionRangeDisplayRed = "None";
    }
} else if ($questionRangeArrayLength > 1) {
    if ($questionRangeArray[0] > 0 && $questionRangeArray[0] < 11) {
        $questionRangeDisplayRed = "Unit ".$questionRangeArray[0];
    } else if ($questionRangeArray[0] > 10 && $questionRangeArray[0] < 21) {
        $questionRangeArrayCalBlue = $questionRangeArray[0] - 10;
        $questionRangeDisplayBlue = "Unit ".$questionRangeArrayCalBlue;
    }

    for ($i = 1; $i < $questionRangeArrayLength; $i++) {
        if ($questionRangeArray[$i] > 0 && $questionRangeArray[$i] < 11) {
            if (empty($questionRangeDisplayRed) == true) {
                $questionRangeDisplayRed = "Unit ".$questionRangeArray[$i];
            } else { // false
                $questionRangeDisplayRed = $questionRangeDisplayRed.", ".$questionRangeArray[$i];
            }
        } else if ($questionRangeArray[$i] > 10 && $questionRangeArray[$i] < 21) {
            $questionRangeArrayCalBlue = $questionRangeArray[$i] - 10;
            if (empty($questionRangeDisplayBlue) == true) {
                $questionRangeDisplayBlue = "Unit ".$questionRangeArrayCalBlue;
            } else { // false
                $questionRangeDisplayBlue = $questionRangeDisplayBlue.", ".$questionRangeArrayCalBlue;
            }
        }
    }

    if (empty($questionRangeDisplayRed) == true) {
        $questionRangeDisplayRed = "None";
    } else { // false
        ;
    }
    if (empty($questionRangeDisplayBlue) == true) {
        $questionRangeDisplayBlue = "None";
    } else { // false
        ;
    }
}

// --------------------------------------------

// 出題品詞-------------------------------------
if ($questionTypeArrayLength == 1) {
    $questionTypeDisplay = $displayTypeArray[$questionTypeArray[0]];
} else if ($questionTypeArrayLength > 1) {
    $questionTypeDisplay = $displayTypeArray[$questionTypeArray[0]];
    for ($i = 1; $i < $questionTypeArrayLength; $i++) {
        $questionTypeDisplay = $questionTypeDisplay.", ". $displayTypeArray[$questionTypeArray[$i]];
    }
}

try {
    $pdo = new PDO(
        'mysql:dbname=mitsuki0217_yume;host=mysql1.php.xdomain.ne.jp;charset=utf8',
        'mitsuki0217_yume',
        'yumetanuser',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // データ取得（改3）----------------------------------------------------------------------
    $sql = "SELECT * FROM words WHERE id BETWEEN";
    if ($questionRangeArrayLength == 1) {
        $questionNumberStart = ($questionRangeArray[0] * 100) - 99;
        $questionNumberEnd = ($questionRangeArray[0] * 100);
        $sql = $sql." $questionNumberStart AND $questionNumberEnd";
    } else {
        $questionNumberStart = ($questionRangeArray[0] * 100) - 99;
        $questionNumberEnd = ($questionRangeArray[0] * 100);
        $sql = $sql." $questionNumberStart AND $questionNumberEnd";
        for ($i = 1; $i < $questionRangeArrayLength; $i++) {
            $questionNumberStart = ($questionRangeArray[$i] * 100) - 99;
            $questionNumberEnd = ($questionRangeArray[$i] * 100);
            $sql = $sql." OR id BETWEEN ".$questionNumberStart." AND ".$questionNumberEnd;
        }
    }

    if ($questionTypeArrayLength == 1) {
        $sql = "SELECT * FROM (".$sql.") table2 WHERE type = '".$questionTypeArray[0]."'";
    } else if ($questionTypeArrayLength == 2 || $questionTypeArrayLength == 3) {
        $sql = "SELECT * FROM (".$sql.") table2 WHERE type = '".$questionTypeArray[0]."'";
        for ($i = 1; $i < $questionTypeArrayLength; $i++) {
            $sql = $sql." OR type = '".$questionTypeArray[$i]."'";
        }
    }
    $stmt = $pdo->query($sql);
    $result_data = $stmt->fetchAll();

    //1から10までの数値から抽選を行うため、1~10の値の配列を生成する
    $result_dataLength = count($result_data);

    if ($result_dataLength < $questionCount) {
        $questionNumberSumDisplay = $result_dataLength;
        $questionNumberSumShuffle = $result_dataLength - 1;
        $questionNumberArrayBefore = range(0, $questionNumberSumShuffle);
        //配列をシャッフルする
        shuffle($questionNumberArrayBefore);
        //配列の上から$questionCount番目まで切り取る（申請問題数より該当問題数が少ないため調整）
        $questionNumberArray = $questionNumberArrayBefore;
    } else if ($result_dataLength == $questionCount || $result_dataLength > $questionCount) {
        $questionNumberSumDisplay = $result_dataLength;
        $questionNumberSumShuffle = $result_dataLength - 1;
        $questionNumberArrayBefore = range(0, $questionNumberSumShuffle);
        //配列をシャッフルする
        shuffle($questionNumberArrayBefore);
        //配列の上から$questionCount番目まで切り取る
        $questionNumberArray = array_slice($questionNumberArrayBefore, 0, $questionCount);
    } else {
        ;
    }
    $questionNumberArrayLength = count($questionNumberArray);
}
catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}
$error_message = htmlspecialchars($error_message);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Yumetan Words Test (Question)</title>
        <link rel="stylesheet" href="css/common.css">
    </head>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H99M98XJ44"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-H99M98XJ44');
    </script>

    <body>
        <header>
            <h2>Yumetan Words Test (Question)</h2>
        </header>
        <div>
            <!-- <p>配列出力テスト欄<br>
            <?php print_r($result_data); ?></p> -->
            <!-- <p>出題番号（id）<br>
            <?php print_r($questionNumberArray); ?></p> -->
            <!-- <p>出題範囲数<br>
            <?php print_r($questionRangeArrayLength); ?></p> -->
            <!-- <p>出題範囲Start<br>
            <?php print_r($questionNumberStart); ?></p> -->
            <!-- <p>出題範囲End<br>
            <?php print_r($questionNumberEnd); ?></p> -->
            <!-- <p>出題範囲 該当数<br>
            <?php print_r($questionNumberSumDisplay); ?>（問）</p> -->
            <!-- <p>出題範囲 該当数（内部処理 0開始）<br>
            0 - <?php echo $questionNumberSumShuffle; ?>問目</p> -->
            <!-- <p>実行SQL<br>
            <?php echo $sql; ?></p> -->
            
            <h3>言語/Language, Go To Question Settings</h3>
            <button class="button button_language" onclick="location.href='index.html'">日本語</button>
            <button class="button button_language" onclick="location.href='index_e.html'">English</button>

            <h3>Question Settings</h3>
            <h4>Question Format (Japanese→English/English→Japanese)</h4>
            <p><?php echo $questionLanguageDisplay ?></p>
            <!-- <h4>出題範囲：<?php echo $questionRangeDisplay ?></h4> -->
            <h4>Question Range (RED/BLUE Yumetan Total 20 Unit)</h4>
            <p>RED Yumetan：<?php echo $questionRangeDisplayRed ?></p>
            <p>BLUE Yumetan：<?php echo $questionRangeDisplayBlue ?></p>
            <h4>Question Part of Speech (Noun/Verb/Adjective)</h4>
            <p><?php echo $questionTypeDisplay ?></p>
            <h4>Within Question Range  Number of Matching Words : <?php print_r($questionNumberSumDisplay); ?>Words</h4>
            <!-- <h4>問題数：全<?php echo $questionCount ?>問</h4> -->
            <h4>The number of questions : <?php echo $questionNumberArrayLength ?> Questions in Total</h4>
            <!-- <h4>正解数：<span id="trueAnswerCount">-</span>問/全<?php echo $questionCount ?>問中</h4> -->
            <h4>The Number of Correct Answers : <span id="trueAnswerCount">-</span> Questions/Out of <?php echo $questionNumberArrayLength ?> Questions</h4>
            <h4>Accuracy Rate : <span id="trueAnswerLevel">-</span>%</h4>
            <h4>[Notes on answers]</h4>
            <p style="margin-bottom: 10px;">
            When filling in the answer column, please use <span class="important">"half-width"</span> when entering <span class="important">English words</span>, and <span class="important">"full-width"</span> when entering <span class="important">Japanese.</span><br>
            Even if you enter an English word correctly in full-width, <span class="important">it will not be judged as correct.</span><br>
            If even one character is incorrect, it will be <span class="important answerFalse">judged as incorrect.</span> <span class="important">A perfect match</span> will <span class="important answerTrue">result in a correct answer.</span>
            <!-- 解答欄に記入する際は、<span class="important">英単語</span>を入力する際は<span class="important">「半角」</span>、<span class="important">日本語</span>を入力する際は<span class="important">「全角」</span>で入力してください。<br>
            全角で英単語を正しく入力しても、<span class="important">正解判定となりません。</span><br>
            1文字でも間違えると、<span class="important answerFalse">不正解判定</span>となります。<span class="important">完全一致</span>で<span class="important answerTrue">正解判定</span>となります。 -->
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
                // 問題数調整前---------------------------------------------------------
                // if ($questionLanguage == 1) {
                //     for ($i = 0; $i < $questionCount; $i++) { 
                //         $questionNumber = $i + 1;
                //         $yumetanNo = ($result_data[$questionNumberArray[$i]]['id'] <= 1000) ? '赤'.$result_data[$questionNumberArray[$i]]['id'] : '青'.($result_data[$questionNumberArray[$i]]['id'] - 1000);
                //         print '<tr>';
                //         print '<td><p>'.$questionNumber.'</p></td>';
                //         print '<td><p>'.$result_data[$questionNumberArray[$i]]['mean'].'</p></td>';
                //         print '<td><input type="text" id="Q'.$questionNumber.'"></td>';
                //         print '<td><p id="C'.$questionNumber.'">-</p></td>';
                //         // print '<td>'.$result_data[$questionNumberArray[$i]]['id'].'</td>';
                //         // print '<td>'.($result_data[$questionNumberArray[$i]]['id'] <= 1000) ? '赤'.$result_data[$questionNumberArray[$i]]['id'] : '青'.($result_data[$questionNumberArray[$i]]['id'] - 1000).'</td>';
                //         print '<td><p>'.$yumetanNo.'</p></td>';
                //         print '<td><p id="A'.$questionNumber.'" class="answer">'.$result_data[$questionNumberArray[$i]]['word'].'</p></td>';
                //         print '<td><p>'.$displayTypeArray[$result_data[$questionNumberArray[$i]]['type']].'</p></td>';
                //         print '</tr>';
                //     }
                // } else if ($questionLanguage == 2) {
                //     for ($i = 0; $i < $questionCount; $i++) { 
                //         $questionNumber = $i + 1;
                //         $yumetanNo = ($result_data[$questionNumberArray[$i]]['id'] <= 1000) ? '赤'.$result_data[$questionNumberArray[$i]]['id'] : '青'.($result_data[$questionNumberArray[$i]]['id'] - 1000);
                //         print '<tr>';
                //         print '<td><p>'.$questionNumber.'</p></td>';
                //         print '<td><p>'.$result_data[$questionNumberArray[$i]]['word'].'</p></td>';
                //         print '<td><input type="text" id="Q'.$questionNumber.'"></td>';
                //         print '<td><p id="C'.$questionNumber.'">-</p></td>';
                //         // print '<td>'.$result_data[$questionNumberArray[$i]]['id'].'</td>';
                //         // print '<td>'.($result_data[$questionNumberArray[$i]]['id'] <= 1000) ? '赤'.$result_data[$questionNumberArray[$i]]['id'] : '青'.($result_data[$questionNumberArray[$i]]['id'] - 1000).'</td>';
                //         print '<td><p>'.$yumetanNo.'</p></td>';
                //         print '<td><p id="A'.$questionNumber.'" class="answer">'.$result_data[$questionNumberArray[$i]]['mean'].'</p></td>';
                //         print '<td><p>'.$displayTypeArray[$result_data[$questionNumberArray[$i]]['type']].'</p></td>';
                //         print '</tr>';
                //     }
                // }
                // -------------------------------------------------------------------

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
                        // print '<td>'.$result_data[$questionNumberArray[$i]]['id'].'</td>';
                        // print '<td>'.($result_data[$questionNumberArray[$i]]['id'] <= 1000) ? '赤'.$result_data[$questionNumberArray[$i]]['id'] : '青'.($result_data[$questionNumberArray[$i]]['id'] - 1000).'</td>';
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
                        // print '<td>'.$result_data[$questionNumberArray[$i]]['id'].'</td>';
                        // print '<td>'.($result_data[$questionNumberArray[$i]]['id'] <= 1000) ? '赤'.$result_data[$questionNumberArray[$i]]['id'] : '青'.($result_data[$questionNumberArray[$i]]['id'] - 1000).'</td>';
                        print '<td><p>'.$yumetanNo.'</p></td>';
                        print '<td><p id="A'.$questionNumber.'" class="answer">'.$result_data[$questionNumberArray[$i]]['mean'].'</p></td>';
                        print '<td><p>'.$displayTypeArray[$result_data[$questionNumberArray[$i]]['type']].'</p></td>';
                        print '</tr>';
                    }
                }
                // ------------------------------------------------------------------

                // for ($i = 0; $i < $questionCount; $i++) { 
                //     $questionNumber = $i + 1;
                //     print '<tr>';
                //     print '<td>'.$questionNumber.'</td>';
                //     print '<td>'.$result_data[$questionNumberArray[$i]]['mean'].'</td>';
                //     print '<td><input type="text" name="Q'.$questionNumber.'"></td>';
                //     print '<td>◯ ×</td>';
                //     print '<td>'.$result_data[$questionNumberArray[$i]]['id'].'</td>';
                //     print '<td>'.$result_data[$questionNumberArray[$i]]['word'].'</td>';
                //     print '</tr>';
                // }
                ?>
            </table>

            <!-- <p>テスト欄<?php print_r($result_data[0]['word']); ?></p> -->

            <script type="text/javascript">
                var questionCount = <?php echo $questionCount ?> + 1;
                function answerCheck(){
                    var elements = document.getElementsByClassName('answer');
                    for(i=0;i<elements.length;i++){
                        elements[i].style.color = "#FF0000";
                    }
                    // document.getElementsByClassName('answer').style.cssText = 'color: red;';

                    var trueAnswerCount = 0;
                    for (let i = 1; i < questionCount; i++) {
                        var questionId = 'Q' + i;
                        var answerId = 'A' + i;
                        var checkId = 'C' + i;
                        var question = document.getElementById(questionId).value;
                        // alert('Q:' + question);
                        var answer = document.getElementById(answerId).innerText;
                        // alert('A:' + answer);

                        // // 英語→日本語用 「～」互換性用プログラム データ上は全角英字「～」
                        // if (answer.indexOf('〜') != -1) { // 半角カタカナ「〜」
                        //     answer = answer.replace('〜', '～');
                        // } else if (answer.indexOf('〜') != -1) { // カタカナ「〜」
                        //     answer = answer.replace('〜', '～');
                        // } else if (answer.indexOf('〜') != -1) { // ひらがな「〜」
                        //     answer = answer.replace('〜', '～');
                        // } else if (answer.indexOf('〜') != -1) { // 無印「〜」
                        //     answer = answer.replace('〜', '～');
                        // } else {
                        //     // alert('入力文字列は全角英字の～です。')
                        //     ;
                        // }
                        if (!question && !answer) {
                            break;
                        } else if (question == answer) {
                            document.getElementById(checkId).innerHTML = '<p class="answerCheck answerTrue">Correct</p>';
                            trueAnswerCount++;
                        } else {
                            document.getElementById(checkId).innerHTML = '<p class="answerCheck answerFalse">Incorrect</p>';
                        }
                        // alert('end:' + i)
                    }
                    
                        // var i = 5;
                        // var questionId = 'Q' + i;
                        // var answerId = 'A' + i;
                        // var checkId = 'C' + i;
                        // var question = document.getElementById(questionId);
                        // var answer = document.getElementById(answerId).value;
                        // if (question == answer) {
                        //     document.getElementById(checkId).innerHTML = '<p class="answerCheck answerTrue">正解</p>';
                        // } else {
                        //     document.getElementById(checkId).innerHTML = '<p class="answerCheck answerFalse">不正解</p>';
                        // }

                    document.getElementById('trueAnswerCount').innerHTML = '<span id="trueAnswerCount">' + trueAnswerCount + '</span>';

                    // var trueAnswerLevel = Math.floor((trueAnswerCount / <?php echo $questionCount ?> * 100) * 10) / 10;
                    // // var trueAnswerLevel = trueAnswerCount / <?php echo $questionCount ?> * 100;
                    var trueAnswerLevel = Math.floor((trueAnswerCount / <?php echo $questionNumberArrayLength ?> * 100) * 10) / 10;
                    // var trueAnswerLevel = trueAnswerCount / <?php echo $questionNumberArrayLength ?> * 100;
                    document.getElementById('trueAnswerLevel').innerHTML = '<span id="trueAnswerLevel">' + trueAnswerLevel + '</span>';
                }
                // function answerDisplay() {
                //     var elements = document.getElementsByClassName('answer');
                //     for(i=0;i<elements.length;i++){
                //         elements[i].style.color = "#FF0000";
                //     }
                //     // document.getElementsByClassName('answer').style.cssText = 'color: red;';
                // }
                function reload() {
                    location.reload();
                }
            </script>

            <button onclick="answerCheck()" class="button button_answerCheck">Match The Answer</button>
            <button onclick="reload()" class="button button_reload">Retry</button>
            <!-- <form action="answer.php" method="POST">
                <p><input type="submit" value="答えあわせ"></p>
            </form> -->
        </div>
    </body>
</html>
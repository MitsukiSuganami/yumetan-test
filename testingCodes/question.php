<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>ユメタン単語テスト（問題）</title>
        <link rel="stylesheet" href="css/common_pc.css" media="screan and (min-width:961px)">
        <link rel="stylesheet" href="css/common_mo.css" media="screan and (max-width:960px)">
    </head>

    <body>
        <header>
            <h2>ユメタン単語テスト（問題）Ver.1.0.2</h2>
        </header>
        <div>
            
            <h3>言語/Language・出題設定</h3>
            <button class="button button_language" onclick="location.href='index.html'">日本語</button>
            <button class="button button_language" onclick="location.href='index_e.html'">English</button>

            <h3>出題設定（Settings）</h3>
            <h4>出題形式（日本語→英語/英語→日本語）</h4>
            <p><?php echo $questionLanguageDisplay ?></p>
            <h4>出題範囲（赤/青ユメタン 合計20Unit）</h4>
            <p>赤ユメタン：<?php echo $questionRangeDisplayRed ?></p>
            <p>青ユメタン：<?php echo $questionRangeDisplayBlue ?></p>
            <h4>出題品詞（名詞/動詞/形容詞）</h4>
            <p><?php echo $questionTypeDisplay ?></p>
            <h4>出題範囲内 該当単語数：<?php print_r($questionNumberSumDisplay); ?>語</h4>
            <h4>問題数：全<?php echo $questionNumberArrayLength ?>問</h4>
            <h4>正解数：<span id="trueAnswerCount">-</span>問/全<?php echo $questionNumberArrayLength ?>問中</h4>
            <h4>正解率：<span id="trueAnswerLevel">-</span>%</h4>
            <h4>【解答上の注意】</h4>
            <p style="margin-bottom: 10px;">
            解答欄に記入する際は、<span class="important">英単語</span>を入力する際は<span class="important">「半角」</span>、<span class="important">日本語</span>を入力する際は<span class="important">「全角」</span>で入力してください。<br>
            全角で英単語を正しく入力しても、<span class="important">正解判定となりません。</span><br>
            1文字でも間違えると、<span class="important answerFalse">不正解判定</span>となります。<span class="important">完全一致</span>で<span class="important answerTrue">正解判定</span>となります。
            </p>

            <h3>問題（Question）</h3>
            <table border="1">
                <tr>
                    <th>問題番号</th>
                    <th>問題</th>
                    <th>解答欄</th>
                    <th>正誤判定</th>
                    <th>ユメタン該当No.</th>
                    <th>解答</th>
                    <th>品詞区分</th>
                </tr>
                <?php

                // 問題数調整後----------------------------------------------------------
                if ($questionLanguage == 1) {
                    for ($i = 0; $i < $questionNumberArrayLength; $i++) { 
                        $questionNumber = $i + 1;
                        $yumetanNo = ($result_data[$questionNumberArray[$i]]['id'] <= 1000 && $result_data[$questionNumberArray[$i]]['id'] >= 1) ? '赤：'.$result_data[$questionNumberArray[$i]]['id'] : '青：'.($result_data[$questionNumberArray[$i]]['id'] - 1000);
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
                        $yumetanNo = ($result_data[$questionNumberArray[$i]]['id'] <= 1000 && $result_data[$questionNumberArray[$i]]['id'] >= 1) ? '赤：'.$result_data[$questionNumberArray[$i]]['id'] : '青：'.($result_data[$questionNumberArray[$i]]['id'] - 1000);
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
                            document.getElementById(checkId).innerHTML = '<p class="answerCheck answerTrue">正解</p>';
                            trueAnswerCount++;
                        } else {
                            document.getElementById(checkId).innerHTML = '<p class="answerCheck answerFalse">不正解</p>';
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

            <button onclick="answerCheck()" class="button button_answerCheck">答えあわせ</button>
            <button onclick="reload()" class="button button_reload">再出題</button>
        </div>
    </body>
</html>
<?php
// 데이터베이스 연결
$servername = "localhost";
$username = "user";
$password = "12345";
$dbname = "jbuddy";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// facility 테이블에서 모든 정보를 가져옴
$sql = "SELECT * FROM facilities";
$result = $conn->query($sql);

// 데이터를 JSON 형식으로 변환
$facilities = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $facilities[] = $row;
    }
}

// 데이터베이스 연결 종료
$conn->close();

// JSON 출력
echo json_encode($facilities);
?>



<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>웹 챗봇</title>
    <style>
        body,
        html {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden; /* 화면 넘침 방지 */
        }
        .container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .chat-container {
            width: 100%; /* 전체 너비를 차지 */
            height: 90%;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .chat-messages {
            flex: 1;
            overflow-y: scroll;
            padding: 10px;
        }
        .chat-message {
            margin-bottom: 10px;
            display: flex;
            align-items: center; /* 메시지와 로고를 수직 정렬 */
        }
        .user-message {
            justify-content: flex-end;
        }
        .user-message .message-bubble {
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            padding: 8px 15px;
            max-width: 70%;
            word-wrap: break-word;
            margin: 5px;
        }
        .bot-message {
            justify-content: flex-start;
        }
        .bot-message .message-bubble {
            background-color: #f0f0f0;
            color: #333;
            border-radius: 5px;
            padding: 8px 15px;
            max-width: 70%;
            word-wrap: break-word;
            margin: 5px;
        }
        .chat-logo {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        /* 추가된 CSS 스타일 */
        .chat-input-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
        }

        .chat-input {
            width: calc(100% - 80px); /* 전체 너비에서 버튼 너비를 빼고 */
            border-radius: 20px; /* 라운드 처리 */
            border: 1px solid #ccc;
            padding: 10px;
            margin-right: 10px;
        }

        .send-button {
            border: none;
            border-radius: 20px; /* 라운드 처리 */
            background-color: #007bff;
            color: #fff;
            padding: 10px 40px; /* 더 긴 padding 값 적용 */
            cursor: pointer;
            white-space: nowrap; /* 텍스트가 한 줄로 표시되도록 */
        }

        .category-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 10px;
        }

        .category-button {
            border: 2px solid #007bff; /* 테두리 추가 */
            border-radius: 20px; /* 동그라미 모양 */
            background-color: #fff; /* 배경색 흰색으로 변경 */
            color: #007bff;
            padding: 15px;
            margin: 5px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1 0 25%; /* 각 버튼이 한 줄에 4개씩 나타나도록 */
            max-width: 25%; /* 각 버튼이 한 줄에 4개씩 나타나도록 */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* 그림자 추가 */
            transition: transform 0.3s ease; /* 변환 효과 추가 */
        }

        .category-button img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }

        .category-button:hover {
            transform: translateY(
                    -5px
            ); /* 마우스를 가져다 대었을 때 버튼이 약간 위로 움직임 */
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15); /* 그림자 약간 크게 설정 */
        }

        .selected {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="chat-container" id="chat-container">
        <div class="chat-messages" id="chat-messages">
            <div class="chat-message bot-message">
                <img src="buddy.png" alt="챗봇 로고" class="chat-logo" />
                <div class="message-bubble">
                    안녕하세요! 중부대학교 챗봇 'J-buddy 입니다. 무엇을
                    도와드릴까요?<br />
                    <div class="category-buttons">
                        <button
                                class="category-button"
                                onclick="selectCategory('(학교소개)')"
                        >
                            <img src="school.png" alt="학교소개" />학교소개
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(입학처)')"
                        >
                            <img src="student.png" alt="입학처" />입학처
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(오시는길)')"
                        >
                            <img src="map.png" alt="오시는길" />오시는길
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(학과정보)')"
                        >
                            <img src="info.png" alt="학과정보" />학과정보
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(학사일정)')"
                        >
                            <img src="date.png" alt="학사일정" />학사일정
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(편의시설)')"
                        >
                            <img src="coffee.png" alt="편의시설" />편의시설
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(수강신청)')"
                        >
                            <img src="computer.png" alt="수강신청" />수강신청
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(등록금)')"
                        >
                            <img src="cash.png" alt="등록금" />등록금
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(대학생활)')"
                        >
                            <img src="friends.png" alt="대학생활" />대학생활
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(학식정보)')"
                        >
                            <img src="food.png" alt="학식정보" />학식정보
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(개발진)')"
                        >
                            <img src="developer.png" alt="개발진" />개발진
                        </button>
                        <button
                                class="category-button"
                                onclick="selectCategory('(오류신고)')"
                        >
                            <img src="chat.png" alt="오류신고" />오류신고
                        </button>
                    </div>
                </div>
            </div>
            <!-- 사용자 메시지는 여기에 추가됩니다. -->
        </div>
        <!-- 수정된 사용자 입력창 -->
        <div class="chat-input-container">
            <input
                    type="text"
                    class="chat-input"
                    id="user-input"
                    placeholder="메시지를 입력하세요..."
                    onkeypress="handleKeyPress(event)"
            />
            <button class="send-button" onclick="sendMessage()">전송</button>
        </div>
        <!-- 수정된 사용자 입력창 끝 -->
    </div>
</div>

<script>
    // 최대 너비를 유동적으로 조정하는 함수
    function adjustMaxWidth() {
        var containerWidth =
            document.getElementById("chat-container").offsetWidth;
        document.querySelector(".chat-container").style.maxWidth =
            containerWidth + "px";
    }

    // 최대 너비를 유동적으로 조정하는 함수
    function adjustMaxWidth() {
        var containerWidth =
            document.getElementById("chat-container").offsetWidth;
        var currentMaxWidth = parseInt(
            document.querySelector(".chat-container").style.maxWidth
        );

        // 현재 최대 너비와 현재 창의 너비가 다를 때만 최대 너비 조정
        if (currentMaxWidth !== containerWidth) {
            document.querySelector(".chat-container").style.maxWidth =
                containerWidth + "px";
        }
    }

    // 대화 데이터: 질문과 대응하는 응답을 포함한 객체
    const conversationData = {
        안녕: "안녕하세요!",
        "오늘 날씨는 어때?": "오늘은 맑은 날씨입니다.",
        "무엇을 도와드릴까요?": "무엇이든 물어보세요.",
        // 필요한 만큼 질문과 대응하는 응답을 추가할 수 있습니다.
    };

    // 학교소개 함수
    function introduceSchool() {
        var schoolIntroduction =
            "중부대학교는 자유를 누리고 진리를 터득하여 미래를 창조하는 ‘바른 인재’, " +
            "전문성을 갖추어 첨단과학기술시대를 이끌 ‘창의 인재’, " +
            "지구촌시대의 인류사회에 공헌할 ‘국제 인재’를 양성하는 “학생성장지향대학”입니다.";
        return schoolIntroduction;
    }
    // 오시는 길 링크 안내 함수
    function showDirections() {
        var directionsMessage =
            "중부대학교에 오시는 길을 보시려면 <a href='https://www.joongbu.ac.kr/menu.es?mid=a10107010000' target='_blank'>여기</a>를 눌러주세요.";
        return directionsMessage;
    }

    function respondWithAdmissionsLink() {
        return "중부대학교 입학처 페이지 링크: <a href='https://www.joongbu.ac.kr/index.es?sid=a6' target='_blank'>입학처 바로가기</a>";
    }
    // 사용자가 전송 버튼을 클릭하거나 엔터 키를 누를 때마다 호출되는 함수
    function handleKeyPress(event) {
        if (event.key === "Enter") {
            sendMessage();
        }
    }

    // 사용자가 전송 버튼을 클릭할 때마다 호출되는 함수
    function sendMessage() {
        // 사용자 입력 가져오기
        var userInput = document.getElementById("user-input").value;

        // 사용자가 입력한 메시지를 채팅창에 표시
        displayMessage("user", userInput);

        // 챗봇에게 사용자 입력을 보냄 (실제 서버로 보내거나 로컬에서 처리할 수 있음)
        var botResponse = getBotResponse(userInput);
        displayMessage("bot", botResponse);

        // 입력 상자 비우기
        document.getElementById("user-input").value = "";
    }

    // 사용자가 입력한 질문에 대한 챗봇의 응답을 반환하는 함수
    function getBotResponse(userInput) {
        // 대화 데이터에서 사용자 질문에 대한 응답을 찾아 반환
        return (
            conversationData[userInput] ||
            "죄송합니다. 이해할 수 없는 질문입니다."
        );
    }

    // 채팅창에 메시지 추가하는 함수
    function displayMessage(sender, message) {
        var chatMessages = document.getElementById("chat-messages");
        var messageClass = sender === "user" ? "user-message" : "bot-message";
        var messageElement = document.createElement("div");
        messageElement.classList.add("chat-message");
        messageElement.classList.add(messageClass);
        var messageBubble = document.createElement("div");
        messageBubble.classList.add("message-bubble");
        messageBubble.innerHTML = message; // Changed to innerHTML to allow line breaks
        messageElement.appendChild(messageBubble);
        if (sender === "bot") {
            var logo = document.createElement("img");
            logo.src = "buddy.png";
            logo.alt = "챗봇 로고";
            logo.classList.add("chat-logo");
            messageElement.insertBefore(logo, messageBubble);
        }
        chatMessages.appendChild(messageElement);

        // 채팅창 스크롤 맨 아래로 이동
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // 편의시설 정보를 가져와서 챗봇 응답으로 표시하는 함수
function showFacilities() {
    // AJAX를 사용하여 PHP 파일에 요청을 보냄
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "get_facilities.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // 요청이 성공하면 JSON 형식으로 전달된 데이터를 가져옴
            var facilitiesData = JSON.parse(xhr.responseText);
            // 챗봇 응답으로 편의시설 정보를 표시
            var botResponse = formatFacilitiesResponse(facilitiesData);
            displayMessage("bot", botResponse);
        }
    };
    xhr.send();
}

// 카테고리 선택 시 스타일 변경 및 챗봇 응답 추가 함수
function selectCategory(category) {
    // 사용자가 선택한 카테고리를 채팅창에 표시
    displayMessage("user", category);
    // 선택한 카테고리에 따라 챗봇 응답 설정
    var botResponse;
    if (category === "(학교소개)") {
        botResponse = introduceSchool();
    } else if (category === "(오시는길)") {
        botResponse = showDirections(); // 오시는 길 링크 안내 함수 호출
    } else if (category === "(입학처)") {
        botResponse = respondWithAdmissionsLink();
    } else if (category === "(편의시설)") {
        // 편의시설 정보를 가져와서 챗봇 응답으로 표시
        showFacilities();
        return; // showFacilities() 실행 후 함수 종료
    } else {
        botResponse = getBotResponse(category);
    }
    // 챗봇 응답 표시
    displayMessage("bot", botResponse);
}

// 편의시설 데이터를 적절한 형식으로 변환하여 반환하는 함수
function formatFacilitiesResponse(facilitiesData) {
    var response = "편의시설 목록:\n";
    for (var i = 0; i < facilitiesData.length; i++) {
        var facility = facilitiesData[i];
        response += "- " + facility.facility_name + ": " + facility.description + "\n";
    }
    return response;
}


        // 선택된 카테고리 버튼 스타일 변경
        var categoryButtons = document.querySelectorAll(".category-button");
        categoryButtons.forEach((button) => {
            button.classList.remove("selected");
        });
        var selectedButton = document.querySelector(
            `.category-button:contains("${category}")`
        );
        selectedButton.classList.add("selected");
    }
</script>
</body>
</html>
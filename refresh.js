// // 獲取要刷新的元素
// var refreshableContent = document.getElementById('refreshable-content');

// // 定義刷新函式
// function refreshContent() {
//     // 在這裡根據需求更新內容
//     var currentTime = new Date().toLocaleTimeString();
//     refreshableContent.innerText = '更新內容：' + currentTime;
// }

// // 每 3 秒刷新一次內容
// setInterval(refreshContent, 3000);


document.addEventListener('DOMContentLoaded', function() {
    var recurringButton = document.getElementById('recurringButton');
    var modal = document.getElementById('myModal');
    var closeModalBtn = document.getElementById('closeModalBtn');

    recurringButton.addEventListener('click', function() {
        modal.style.display = 'block';
    });

    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
});

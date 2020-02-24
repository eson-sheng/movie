let dplayer = document.getElementById('dplayer');

/*屏幕自适应*/
refresh();
window.onresize = function () {
    setTimeout(function () {
        refresh();
    }, 10)
};

function refresh() {
    dplayer.style.height = document.scrollingElement.clientHeight * 0.8 + 'px';
    dplayer.style.width = '72%';
}

/*播放器*/
const dp = new DPlayer({
    container: dplayer,
    video: {
        url: './video/' + json.name + '.mp4',
        pic: './video/thum/' + json.name + '.jpg',
    },
    autoplay: true,
    screenshot: true,
    theme: 'pink',
    contextmenu: [],
});
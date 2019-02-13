/* 存放全局变量 */
var _GLBConfig = {
    URL: 'http://tt.wapwei.com',

    MAX_PLAYER_COUNT: 2,
    GAME_START_EVENT: "gameStart",
    NEW_START_EVENT: "newStar",
    PLAYER_MOVE_EVENT: "playerMove",
    GAIN_SCORE_EVENT: "gainScore",
    PLAYER_POSITION_EVENT: "playerPosition",
    MAP_INIT: 'mapInit',
    USER_ALIKE: 'userAlike',
    SOCKET_SCORE: 'socketScore',
    SOCKET_CUBE: 'socketCube',
    GAME_READY: "gameReady",
    SCENE_READY: "sceneReady",
    OneMap: true,

    channel: 'MatchVS',
    platform: 'alpha',//测试环境：alpha,线上环境：release
    gameId: 214352,
    gameVersion: 1,
    appKey: '9eedd013bf8e4cba87a158be630f6817#M',
    secret: '0d857660381049cd925cd14c602bf7f7',

    userInfo: {},
    playerUserIds: [],
    isRoomOwner: false,
    events: {},
};
let _GLBFun = {
    ajax(options) {
        options = options || {};
        options.type = (options.type || "GET").toUpperCase();
        options.url = options.url;
        options.dataType = options.dataType || "json";
        options.async = options.async || true;
        let params = this.formatParams(options.data);
        let xhr;

        //创建 - 第一步
        if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        } else if (window.ActiveObject) { //IE6及以下
            xhr = new ActiveXObject('Microsoft.XMLHTTP');
        }

        //连接 和 发送 - 第二步
        if (options.type === "GET") {
            console.log(params);
            xhr.open("GET", options.url + "?" + params, options.async);
            xhr.send(null);
        } else if (options.type === "POST") {
            xhr.open("POST", options.url, options.async);
            //设置表单提交时的内容类型
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send(params);
        }

        //接收 - 第三步
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                let status = xhr.status;
                if (status >= 200 && status < 300 || status === 304) {
                    options.success && options.success(JSON.parse(xhr.responseText, xhr.responseXML));
                } else {
                    options.error && options.error(status);
                }
            }
        }
    },
//格式化参数
    formatParams(data) {
        let arr = [];
        for (let name in data) {
            arr.push(encodeURIComponent(name) + "=" + encodeURIComponent(data[name]));
        }
        arr.push(("v=" + Math.random()).replace(".", ""));
        return arr.join("&");
    },
};
module.exports = {_GLBConfig,_GLBFun};
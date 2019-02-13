var mvs = require('Mvs');
var GLB = require('GLBConfig');
// import {_GLBConfig,_MYGLB} from 'GLBConfig';

cc.Class({
    extends: cc.Component,

    properties: {
        labelInfo: {
            default: null,
            type: cc.Label,
        },
        playerList: {
            default: null,
            type: cc.Label,
        },
        joinSingleBtn:{
            default: null,
            type: cc.Button,
        },
        joinRoomBtn:{
            default: null,
            type: cc.Button,
        },
        leavenRoomBtn:{
            default: null,
            type: cc.Button,
        }
    },

    onLoad: function () {
        this.joinSingleBtn.node.on(cc.Node.EventType.TOUCH_START,  () => {
            this.labelLog('开始进入单机模式,请稍等');
            this.changeScene(window.Game.type.singleGame);
        });
        this.joinRoomBtn.node.on(cc.Node.EventType.TOUCH_START,  () => {
            this.labelLog('开始进入房间,请稍等');
            let result = mvs.engine.joinRandomRoom(GLB._GLBConfig.MAX_PLAYER_COUNT, '');
            if (result !== 0)
                return this.labelLog('进入房间失败,错误码:' + result);
        });
        this.leavenRoomBtn.node.on(cc.Node.EventType.TOUCH_START, () => {
            let result = mvs.engine.leaveRoom("I Love China");
            if (result !== 0)
                return this.labelLog('退出房间失败,错误码:' + result);
        });

        mvs.response.initResponse = this.initResponse.bind(this);
        mvs.response.joinRoomNotify = this.joinRoomNotify.bind(this);
        mvs.response.joinRoomResponse = this.joinRoomResponse.bind(this);
        mvs.response.leaveRoomResponse = this.leaveRoomResponse.bind(this);
        mvs.response.leaveRoomNotify = this.leaveRoomNotify.bind(this);
        this.labelLog('开始初始化');
        // mvs.response.getHostListResponse = function(){};
        var result = mvs.engine.init(mvs.response, GLB._GLBConfig.channel, GLB._GLBConfig.platform, GLB._GLBConfig.gameId);
        // mvs.engine.getHostList && mvs.engine.getHostList();
        if (result !== 0) this.labelLog('初始化失败,错误码:' + result);

    },
    loadCallback(){
        cc.director.preloadScene('NewGameScene',  () => {
            this.labelLog('加载完成');
        });
    },
    joinRoomNotify:function(roomUserInfo){
        GLB._GLBConfig.playerUserIds.push(roomUserInfo.userID);
        this.playerList.string  = GLB._GLBConfig.playerUserIds;
    },
    leaveRoomResponse: function(leaveRoomRsp) {
        if (leaveRoomRsp.status === 200) {
            console.log("离开房间成功");
        } else {
            console.log("离开房间失败"+leaveRoomRsp.status);
        }
    },
    leaveRoomNotify:function(leaveRoomInfo) {
        let index = GLB._GLBConfig.playerUserIds.indexOf(leaveRoomInfo.userID);
        GLB._GLBConfig.playerUserIds.splice(index,1);
        this.playerList.string  = GLB._GLBConfig.playerUserIds;
    },
    recordPlayerUserIds: function (userIds) {
        GLB._GLBConfig.playerUserIds = [GLB._GLBConfig.userInfo.id];

        for (var i = 0, l = userIds.length; i < l; i++) {
            var userId = userIds[i];
            if (userId !== GLB._GLBConfig.userInfo.id) {
                GLB._GLBConfig.playerUserIds.push(userId)
            }
        }
    },

    initResponse: function(status) {
        this.labelLog('初始化成功，开始注册用户');
        mvs.response.registerUserResponse = this.registerUserResponse.bind(this); // 用户注册之后的回调
        var result = mvs.engine.registerUser();
        if (result !== 0)
            return this.labelLog('注册用户失败，错误码:' + result);
        else
            this.labelLog('注册用户成功');
    },

    registerUserResponse: function (userInfo) {
        var deviceId = 'abcdef';
        var gatewayId = 0;
        GLB._GLBConfig.userInfo = userInfo;

        this.labelLog('开始登录,用户Id:' + userInfo.id);

        mvs.response.loginResponse = this.loginResponse.bind(this); // 用户登录之后的回调
        var result = mvs.engine.login(userInfo.id, userInfo.token,
            GLB._GLBConfig.gameId, GLB._GLBConfig.gameVersion,
            GLB._GLBConfig.appKey, GLB._GLBConfig.secret,
            deviceId, gatewayId);

        if (result !== 0)
            return this.labelLog('登录失败,错误码:' + status);
    },

    loginResponse: function (info) {
        if (info.status !== 200)
            return this.labelLog('登录失败,异步回调错误码:' + info.status);
        else
            this.labelLog('登录成功');
        this.loadCallback();
    },

    joinRoomResponse: function (status, userInfoList, roomInfo) {
        if (status !== 200) {
            return this.labelLog('进入房间失败,异步回调错误码: ' + status);
        } else {
            this.labelLog('进入房间成功');
            this.labelLog('房间号: ' + roomInfo.roomID);
       }

        var userIds = [GLB._GLBConfig.userInfo.id];
        userInfoList.forEach(function(item) {if (GLB._GLBConfig.userInfo.id !== item.userId) userIds.push(item.userId)});
        this.labelLog('房间用户: ' + userIds);
        mvs.response.sendEventNotify = this.sendEventNotify.bind(this); // 设置事件接收的回调
        mvs.response.gameServerNotify = this.gameServerNotify.bind(this);//接收gameServer的消息
        GLB._GLBConfig.playerUserIds = userIds;
        this.playerList.string = GLB._GLBConfig.playerUserIds;
        //发送准备信息给gameServer
        this.gameReady();
        if (roomInfo.owner === GLB._GLBConfig.userInfo.userID) {
            GLB._GLBConfig.isRoomOwner = true; //设置谁是房主
        }
    },

    sendEventResponse: function (info) {
        if (!info
            || !info.status
            || info.status !== 200) {
            return this.labelLog('事件发送失败')
        }
    },

    gameReady: function(){
        var event = {
            action : GLB._GLBConfig.GAME_READY
        };
        mvs.response.sendEventResponse = this.sendEventResponse.bind(this);// 设置事件发射之后的回调
        /**
         * param 1 : msType 0-客户端+not CPS    1-not 客户端+CPS   2-客户端+CPS
         * param 2 : 用户数据
         * param 3 : destType 可默认为0
         * param 4 : 发送的userID集合
         */
        var result = mvs.engine.sendEventEx(1,JSON.stringify(event), 0, GLB._GLBConfig.playerUserIds);
        if (result.result !== 0)
            return this.labelLog('发送游戏准备通知失败，错误码' + result.result);
        GLB._GLBConfig.events[result.sequence] = event;
        this.labelLog("发起游戏开始的通知，等待回复");
    },

    sendEventNotify: function (info) {
        // if (info
        //     && info.cpProto
        //     && info.cpProto.indexOf(GLB.GAME_START_EVENT) >= 0) {
        //
        //     GLB.playerUserIds = [GLB.userInfo.id]
        //     // 通过游戏开始的玩家会把userIds传过来，这里找出所有除本玩家之外的用户ID，
        //     // 添加到全局变量playerUserIds中
        //     JSON.parse(info.cpProto).userIds.forEach(function(userId) {
        //         if (userId !== GLB.userInfo.id) GLB.playerUserIds.push(userId)
        //     });
        //     this.startGame()
        // }
    },

    gameServerNotify:function(info){
        let obj =JSON.parse(info.cpProto);
        if (obj.action === GLB._GLBConfig.GAME_START_EVENT && info && info.cpProto && info.cpProto.indexOf(GLB._GLBConfig.GAME_START_EVENT) >= 0) {

            GLB._GLBConfig.playerUserIds = [GLB._GLBConfig.userInfo.id];
            // 通过游戏开始的玩家会把userIds传过来，这里找出所有除本玩家之外的用户ID，
            // 添加到全局变量playerUserIds中
            obj.userIds.forEach(function(userId) {
                if (userId !== GLB._GLBConfig.userInfo.id) GLB._GLBConfig.playerUserIds.push(userId)
            });
            this.startGame()
        }
    },
    labelLog: function (info) {
        this.labelInfo.string += '\n' + info;
    },

    startGame: function () {
        this.labelLog('游戏即将开始');
        let result = mvs.engine.joinOver("关闭房间");
        console.log("joinOver result"+result);
        this.changeScene(window.Game.type.OneVsOne)
    },
    changeScene:function(gameType){
        window.Game.currentType = gameType;
        cc.director.loadScene('NewGameScene');
    },
});
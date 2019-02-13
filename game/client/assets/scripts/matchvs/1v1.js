import {Game} from "../New/GameData";

var mvs = require('Mvs');
var GLB = require('GLBConfig');

cc.Class({
    extends: cc.Component,

    properties: {
        round: 0,
        GD:{
            default:null,
            type:cc.Object,
        },//GameData游戏数据
        AN:{
            default:null,
            type:cc.Object,
        },//AssetsNode资源节点
        UM:{
            default:null,
            type:cc.Object,
        },
        LBtn:{
            default:null,
            type:cc.Node,
        },
        RBtn:{
            default:null,
            type:cc.Node,
        },
        player1:{
            default:null,
            type:cc.Node,
        },
        player2:{
            default:null,
            type:cc.Node,
        },
        playerAni1:{
            default:null,
            type:cc.Animation,
        },
        playerAni2:{
            default:null,
            type:cc.Animation,
        },
        isDown:true,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.GD=cc.find("GameData").getComponent("GameData");
        this.AN=cc.find("AssetsNode").getComponent("AssetsNode");
        this.UM=cc.find("UiManager").getComponent("UiManager");
        mvs.response.gameServerNotify = this.gameServerNotify.bind(this);//接收gameServer的消息
        mvs.response.sendEventNotify = this.sendEventNotify.bind(this);
        this.requestMap();

        this.LBtn.on(cc.Node.EventType.TOUCH_START,  () => {
            this.Click("L");
        });
        this.RBtn.on(cc.Node.EventType.TOUCH_START,  () => {
            this.Click("R");
        });

        cc.director.preloadScene('NewGameScene', function () {
            cc.log('Next scene preloaded');
        });
    },

    sendEventNotify(eventInfo){
        let obj = JSON.parse(eventInfo.cpProto);

        this.GD.playerTargetX_2 = this.GD.cubeXArr[obj.score];
        this.GD.playerTargetY_2 = (obj.score * 57)+100;

        this.animPlay(this.playerAni2,"jump");
    },

    requestMap(){
        let event = {
            action: GLB.MAP_INIT,
            round: this.round,
        };
        /**
         * param 1 : msType 0-客户端+not CPS    1-not 客户端+CPS   2-客户端+CPS
         * param 2 : 用户数据
         * param 3 : destType 可默认为0
         * param 4 : 发送的userID集合
         */
        var result = mvs.engine.sendEventEx(1,JSON.stringify(event), 0, GLB.playerUserIds);
        this.round++;
        if (!result || result.result !== 0)
            return console.error('请求地图包失败');
    },
    userAlike(){
        let event = {
            action: GLB.USER_ALIKE,
            score: this.GD.score,
        };
        let arr = GLB.playerUserIds;
        let index = arr.indexOf(GLB.userInfo.id);
        if (index > -1) {
            arr.splice(index, 1);
        }
        let result = mvs.engine.sendEventEx(0,JSON.stringify(event), 0, arr);
        if (!result || result.result !== 0)
            return console.error('用户信息同步失败');
    },

    Click(lr){
        if(this.GD.score%10 === 0){
            this.requestMap();
        }
        this.GD.isClick=true;
        this.GD.life-=2;
        // this.Ran();
        this.CubeCreate();
        this.GameMove(lr);
        this.xDie();
        this.userAlike();
    },//点击执行的函数

    StartCreate(){
        // this.Ran();//做判断是否要随机十个数据
        for(let i=1;i<=10;i++){//游戏刚开始生成随机10个方块
            this.CubeCreate();
        }
    },//初始创建10个方块

    gameServerNotify(info){
        console.log("info:",info);
        let obj = JSON.parse(info.cpProto);
        console.log("obj",obj);
        switch(obj.action){
            case GLB.MAP_INIT:
                this.GD.cubeXArr[0]=350;
                obj.mapPack.forEach( (v) => {
                    this.GD.cubeXArr.push(v);
                });
                // this.GD.cubeXArr.push(obj.mapPack);
                this.GameState(Game.state.start,Game.type.OneVsOne);
                console.log("地图包现在是：",this.GD.cubeXArr);
                break;
            case GLB.USER_ALIKE:
                console.log("正在同步用户信息");
                break;
        }

    },

    CubeCreate(){
        this.GD.cubeIndex++;
        let cube =cc.instantiate(this.AN.cubePrefabs[0]);//动态生成预制体
        cube.parent=this.AN.Root;//将生成出来的预制体设置他的父对象
        cube.setPosition(this.GD.cubeXArr[this.GD.cubeIndex],100+(this.GD.cubeIndex*57));//设置生成后的(x,y)
        cube.zIndex=-this.GD.cubeIndex;

    },//创建一个cube

    GameMove(lr){
        this.GD.score++;//加分
        this.GD.playerTargetY+=57;
        this.GD.gameObjTargetY-=57;
        this.animPlay(this.playerAni1,"jump");
        this.LeftOrRight(lr);
    },//计算PlayerTarget GameObjTarget score 并判断死亡 动画播放

    LeftOrRight(lr){
        if (lr==="L"){
            this.GD.playerTargetX-=100;
            this.GD.gameObjTargetX+=100;

        }else if (lr==="R") {
            this.GD.playerTargetX+=100;
            this.GD.gameObjTargetX-=100;
        }else {
            console.log("Click null")
        }
    },//判断点击 左 or 右

    xDie(){
        console.log('x',this.GD.playerTargetX,this.GD.cubeXArr[this.GD.score]);
        if(this.GD.playerTargetX!==this.GD.cubeXArr[this.GD.score]){
            //碰撞死亡
            this.GD.score-=1;
            this.animPlay(this.playerAni1,"out");
            setTimeout(() =>{
                this.player1.zIndex = -1;
            }, 250);
            this.isDown=false;
            this.GameState(Game.state.over,Game.type.OneVsOne);
        }
    },//判断X死亡方式 并切换游戏状态

    yDie(){
        if (this.GD.isClick) {
            this.GD.life=cc.misc.lerp(this.GD.life,this.GD.score+1,this.GD.speed);
        }
        if(this.GD.life>=this.GD.score&&this.isDown){
            this.animPlay(this.playerAni1,"out");
            this.GameState(Game.state.over,Game.type.OneVsOne);
            this.isDown=false;
        }
    },

    animPlay(ani,aniName){
        ani.play(aniName);
    },//播放动画 (动画对象,动画名字)

    //游戏状态执行
    GameState(val,gameType){
        if(gameType === 1){//进入1v1
            if(val === 0){

            }else if(val===1){
                this.StartCreate();
                console.log("GameStart")
            }else if(val===2){
                this.UM.UiState(Game.state.over);
                // this.SaveData();
                console.log("GameOver");
            }else{
                console.log("GameState null");
            }
        }else{

        }


    },


    start () {

    },

    update (dt) {
        this.yDie();
        this.player2.x=cc.misc.lerp(this.player2.x,this.GD.playerTargetX_2,dt*10);
        this.player2.y=cc.misc.lerp(this.player2.y,this.GD.playerTargetY_2,dt*10);
    },
});

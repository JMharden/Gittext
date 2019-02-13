var mvs = require('Mvs');
var GLB = require('GLBConfig');

cc.Class({
    extends: cc.Component,

    properties: {
        round: 0,
        otherUserScore: 0,
        GD:{
            default:null,
            type:cc.Object,
        },//GameData游戏数据
        VS:{
            default:null,
            type:cc.Object,
        },//ViewShow显示视图
        AN:{
            default:null,
            type:cc.Object,
        },//AssetsNode资源节点
        UM:{
            default:null,
            type:cc.Object,
        },//UiManager控制游戏界面
        seed: 0,
    },
    onLoad () {
        this.GD=cc.find("GameData").getComponent("GameData");
        this.VS=cc.find("ViewShow").getComponent("ViewShow");
        this.AN=cc.find("AssetsNode").getComponent("AssetsNode");
        this.UM=cc.find("UiManager").getComponent("UiManager");
        mvs.response.gameServerNotify = this.gameServerNotify.bind(this);//接收gameServer的消息
        mvs.response.sendEventNotify = this.sendEventNotify.bind(this);
        this.loadCallback();
    },
    loadCallback(){
        console.log("资源已经加载完成");
        this.GameState(window.Game.state.start,window.Game.currentType);
    },

    start() {

    },

    update(dt){
        this.isDie();
    },

    Click(lr){
        this.GD.isClick=true;
        this.Ran();
        this.GD.life-=2;
        this.GameMove(lr);
        this.AN.score.string = this.GD.score;
        if(window.Game.currentType === window.Game.type.OneVsOne){
            this.socketInfo(GLB._GLBConfig.SOCKET_SCORE,this.GD.score);
            if(this.GD.score>this.otherUserScore){
                this.CubeCreate();
                this.socketInfo(GLB._GLBConfig.SOCKET_CUBE);
            }
        }else{
            this.CubeCreate();
        }
    },//点击执行的函数

    StartCreate(){
        this.GD.cubeXArr[0]=350;
        for(let i=1;i<=20;i++){//游戏刚开始生成随机20个方块
            this.Ran();
            this.CubeCreate();
        }
    },//初始创建20个方块

    CubeCreate(){
        this.GD.cubeIndex++;
        this.PrefabCreate(this.AN.roadPrefabs[0],this.AN.Root,cc.v2(this.GD.cubeXArr[this.GD.cubeIndex],100+(this.GD.cubeIndex*50)),-this.GD.cubeIndex);
        this.SceneCreate();
    },//创建一次水平轴的方块 包括路 场景
    PrefabCreate(prefab,parent,v2,zIndex){
        let cube =cc.instantiate(prefab);
        cube.parent=parent;
        cube.setPosition(v2);
        cube.zIndex=zIndex;
        return cc.v2(cube.getPosition());
    },//生成预制体

    PlayerCreate(){
        let cube =cc.instantiate(this.AN.Player[0]);
        cube.parent=this.AN.GameObj;
        cube.setPosition(cc.v2(350,100));
        this.AN.Player[this.AN.Player.length]=cube;
        this.GD.playerTargetX[this.GD.playerTargetX.length]=350;
        this.GD.playerTargetY[this.GD.playerTargetY.length]=100;
    },

    SceneCreate(){

        for(let j=1;j<this.returnRandom(6, 8);j++) {
            this.sceneCreate(1,j);
            this.sceneCreate(-1,j);
        }
        this.GD.houseLTemp--;
        this.GD.houseRTemp--;
    },//每次水平轴生成场景 场景生成
    sceneCreate(lr,index){
        if (lr===-1||lr===1){
            if (this.Rate(7-index,1)) {
                let rockV2 = this.PrefabCreate(this.AN.rockR, this.AN.Root, cc.v2(this.GD.cubeXArr[this.GD.cubeIndex] + ((index * 200)*lr), 100 + (this.GD.cubeIndex * 50)), -this.GD.cubeIndex);
                if (index === 1) {
                    //this.PrefabCreate(this.AN.obstacle, this.AN.Root, rockV2, -this.GD.cubeIndex);
                }
                if (index > 3 && this.Rate(4,1)) {
                    this.PrefabCreate(this.AN.tree, this.AN.Root, rockV2, -this.GD.cubeIndex);
                }if (lr===1){
                    if (index>1&&rockV2.x - ((100 * 5)*lr) > this.GD.cubeXArr[this.GD.cubeIndex + 3] && this.GD.houseRTemp <= 0) {
                        this.PrefabCreate(this.AN.housePrefabs[this.Rate(this.AN.housePrefabs.length,1)], this.AN.Root, cc.v2(this.GD.cubeXArr[this.GD.cubeIndex] + ((index * 200)*lr), 100 + (this.GD.cubeIndex * 50)), -this.GD.cubeIndex);
                        this.GD.houseRTemp = this.returnRandom(10, 15);
                    }
                }
                else if(lr===-1){
                    if (index>3&&rockV2.x - ((100 * 5)*lr) < this.GD.cubeXArr[this.GD.cubeIndex + 3] && this.GD.houseLTemp <= 0) {
                        this.PrefabCreate(this.AN.housePrefabs[this.Rate(this.AN.housePrefabs.length,1)], this.AN.Root, cc.v2(this.GD.cubeXArr[this.GD.cubeIndex] + ((index * 200)*lr), 100 + (this.GD.cubeIndex * 50)), -this.GD.cubeIndex);
                        this.GD.houseLTemp = this.returnRandom(10, 15);
                    }
                }
            }
            else if (index < 3) {
                this.PrefabCreate(this.AN.waterR, this.AN.Root, cc.v2(this.GD.cubeXArr[this.GD.cubeIndex] + ((index * 200)*lr), 100 + (this.GD.cubeIndex * 50)), -this.GD.cubeIndex)
            }else if(index===3){
                if (this.Rate(2,1)){
                    this.PrefabCreate(this.AN.waterR, this.AN.Root, cc.v2(this.GD.cubeXArr[this.GD.cubeIndex] + ((index * 200)*lr), 100 + (this.GD.cubeIndex * 50)), -this.GD.cubeIndex);
                }else{
                    this.PrefabCreate(this.AN.sand, this.AN.Root, cc.v2(this.GD.cubeXArr[this.GD.cubeIndex] + ((index * 200)*lr), 100 + (this.GD.cubeIndex * 50)), -this.GD.cubeIndex);
                }
            }
            else if (index >= 3) {
                this.PrefabCreate(this.AN.sand, this.AN.Root, cc.v2(this.GD.cubeXArr[this.GD.cubeIndex] + ((index * 200)*lr), 100 + (this.GD.cubeIndex * 50)), -this.GD.cubeIndex);
            }

        } else{
            console.log("场景生成方向参数无效");
        }

    },
    /**
     * @return {boolean}
     */
    Rate(Part,son){
        return Math.floor(this.seededRandom() * Part) <= (son - 1);

    },//概率方法 part分母 son分子

    GameMove(lr){
        this.GD.score++;//加分
        this.GD.playerTargetY[0]+=50;
        this.GD.gameObjTargetY-=50;
        this.animPlay(this.AN.player,"jump");
        this.animPlay(this.AN.shadow,"jumpShadow");
        this.LeftOrRight(lr);
    },//计算PlayerTarget GameObjTarget score 并判断死亡 动画播放

    LeftOrRight(lr){
        if (lr==="L"){
            this.GD.playerTargetX[0]-=100;
            this.GD.gameObjTargetX+=100;
        }else if (lr==="R") {
            this.GD.playerTargetX[0]+=100;
            this.GD.gameObjTargetX-=100;
        }else {
            console.log("Click null")
        }
    },//判断点击 左 or 右sceneCreate

    isDie(){
        if (this.GD.isClick) {
            this.GD.life=cc.misc.lerp(this.GD.life,this.GD.score+1,this.GD.speed);
            if(this.GD.playerTargetX[0]!==this.GD.cubeXArr[this.GD.score]){
                this.GD.isDie=true;
                setTimeout(() =>{
                        this.AN.Player[0].zIndex = -1;
                }, 250);
            }else if (this.GD.life>=this.GD.score){
                this.GD.isDie=true;
            }

        }//判断是否死亡

        if (this.GD.isDie){
            this.GD.isClick=false;
            this.GD.isDie=false;
            this.animPlay(this.AN.player,"out");

            this.GameState(window.Game.state.over,window.Game.currentType);
        }//死亡后执行的操作

    },//判断死亡 死亡后的执行



    Ran(){
        if (this.GD.repeatTemp <= 0) {//随机方向与同时生成的方框
            this.GD.LR = this.returnNum(-1, 1);
            this.GD.repeatTemp = this.returnRandom(1, 3);
        }
        this.GD.cubeXArr.push(this.GD.cubeX += (this.GD.LR * 100));
        this.GD.repeatTemp--;
    },

    seededRandom(max, min) {
        max = max || 1;
        min = min || 0;
        this.seed = (this.seed * 9301 + 49297) % 233280;
        let rnd = this.seed / 233280.0;
        return min + rnd * (max - min);
    },

    returnNum(min,max){
        if (this.seededRandom() < 0.5) {
            return min;
        } else {
            return max;
        }
    },//返回二个数的其中之一

    returnRandom(min,max){
        let choices = max - min + 1;
        return Math.floor(this.seededRandom() * choices + min);
    },//返回两个数之间的随机整数

    returnMap(min,max){
        let choices = max - min + 1;
        return Math.floor(Math.random() * choices + min);
    },

    animPlay(ani,aniName){
        ani.play(aniName);
    },//播放动画 (动画对象,动画名字)

    requestMap(){
        let event = {
            action: GLB._GLBConfig.MAP_INIT,
            round: this.round,
        };
        let result = mvs.engine.sendEventEx(1,JSON.stringify(event), 0, GLB._GLBConfig.playerUserIds);
        this.round++;
        if (!result || result.result !== 0)
            return console.error('请求地图包失败');
    },
    gameServerNotify(info){
        let obj = JSON.parse(info.cpProto);
        switch(obj.action){
            case GLB._GLBConfig.GAME_START_EVENT:
                console.log("游戏开始");
                break;
            case GLB._GLBConfig.MAP_INIT:
                console.log("请求的地图包：",obj);
                this.seed = obj.mapPack;
                this.StartCreate();
                break;
            case GLB._GLBConfig.USER_ALIKE:
                console.log("正在同步用户信息");
                break;
            default:
                break;
        }

    },
    socketInfo(type,val){
        let event = {
            action: type,
            score: val || '',
        };
        let arr = GLB._GLBConfig.playerUserIds;
        let index = arr.indexOf(GLB._GLBConfig.userInfo.id);
        if (index > -1) {
            arr.splice(index, 1);
        }
        let result = mvs.engine.sendEventEx(0,JSON.stringify(event), 0, arr);
        if (!result || result.result !== 0)
            return console.error('用户信息同步失败');
    },
    sendEventNotify(eventInfo){
        let obj = JSON.parse(eventInfo.cpProto);
        switch (obj.action) {
            case GLB._GLBConfig.SOCKET_SCORE:
                this.otherUserScore = obj.score;
                this.GD.playerTargetX[1] = this.GD.cubeXArr[this.otherUserScore];
                this.GD.playerTargetY[1] = (this.otherUserScore * 50)+100;
                let player = this.AN.Player[1].getChildByName("player");
                // console.log(player);
                // this.animPlay(player,"jump");
                break;
            case GLB._GLBConfig.SOCKET_CUBE:
                this.Ran();
                this.CubeCreate();
                break;
            default:
                console.log("来历不明的请求信息");
                break;

        }

    },

    //游戏状态执行
    GameState(val,type){
        switch (val){
            case 0:
                console.log("game default");
                break;
            case 1: //游戏开始
                this.GD.playerTargetX[0] = 350;
                this.GD.playerTargetY[0] = 100;
                if(type === window.Game.type.singleGame){
                    console.log("single Game tart");
                    this.seed = this.returnMap(1,1000);
                    this.StartCreate();
                }else if(type === window.Game.type.OneVsOne){
                    console.log("OneVsOne Game Start");
                    if(GLB._GLBConfig.isRoomOwner){
                        this.requestMap();
                    }
                    this.PlayerCreate();
                }
                break;
            case 2: //游戏结束
                if(type === window.Game.type.singleGame){
                    console.log("single Game Over");
                    console.log(this.generateUUID());
                }else if(type === window.Game.type.OneVsOne){
                    console.log("OneVsOne Game Over");
                    this.SeverData();
                }
                console.log("地图包现在是：",this.GD.cubeXArr);
                this.UM.UiState(window.Game.state.over);
                break;
            default:
                console.warn("Game State Unknown Error");
                break;
        }

    },
    generateUUID() {
        var d = new Date().getTime();
        if (window.performance && typeof window.performance.now === "function") {
            d += performance.now(); //use high-precision timer if available
        }
        var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = (d + Math.random() * 16) % 16 | 0;
            d = Math.floor(d / 16);
            return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
        return uuid;
    },

    SeverData(){},

    //加载场景 SceName为场景名称
    LoadScene(SceName){
        cc.director.loadScene(SceName);
    },

});

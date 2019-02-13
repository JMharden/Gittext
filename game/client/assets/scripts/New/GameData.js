window.Game = {
    state:{ //游戏状态
        default: 0,
        start: 1,
        over: 2,
    },
    type:{ //游戏类型
        singleGame: 0,//单机模式
        OneVsOne: 1,//1v1对战模式
    },
    currentType: 0, //当前的游戏状态
};

cc.Class({
    extends: cc.Component,
    properties:() => ({
        score:0,
        isClick:false,
        isDie:false,
        life:-5,
        speed:0.02,
        maxSpeed:0.1,
        cubeIndex:0,
        cubeXArr:[0],
        cubeX:350,//存储随机生成最后 cube的 X值
        ranCubeXArr:[0],//临时存储随机10个 cube
        LR:1,//随机方向 L:-1 R:1
        repeatTemp:0,//同时一个方向生成多少
        houseLTemp:0,
        houseRTemp:0,
        playerTargetX: {
            default:[],
            type: cc.Float,
        },//玩家移动目标点
        playerTargetY: {
            default:[],
            type: cc.Float,
        },//
        gameObjTargetX: 0,//gameObj移动目标点
        gameObjTargetY: 0,//
        outTime: 3000,
    }),
});
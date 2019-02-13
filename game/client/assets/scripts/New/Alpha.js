cc.Class({
    extends: cc.Component,

    properties: {
        GD:{
            default:null,
            type:cc.Object,
        },//游戏数据
        house:{
            default: null,
            type:cc.Node,
        },
        scopeSize:0,
        high:0

    },
    onLoad () {
        this.GD = cc.find("GameData").getComponent("GameData");
    },
    update(dt){
        if (this.GD.playerTargetY[0]>=this.node.y+(this.scopeSize*50)&&this.GD.playerTargetY[0]<=this.node.y+(this.high*100)&&this.GD.playerTargetX[0]<=this.node.x+(this.scopeSize*100)&&this.GD.playerTargetX[0]>=this.node.x-(this.scopeSize*100)){
            this.house.opacity=cc.misc.lerp(this.house.opacity,50,dt*5);
        }else
        {
            this.house.opacity=255;
        }

    },
});

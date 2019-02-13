
cc.Class({
    extends: cc.Component,

    properties: {
        GD:{
            default:null,
            type:cc.Object,
        },//游戏数据

        cube:{
            default:null,
            type:cc.Animation,
        },
        Cube:{
            default:null,
            type:cc.Node,
        },
        isOut:true,
        meIndex:0,
    },
    onLoad(){
        this.GD=cc.find("GameData").getComponent("GameData");

        this.meIndex=this.GD.cubeIndex;
        this.GD.speed=cc.misc.lerp(this.GD.speed,this.GD.maxSpeed,0.001);
    },
    start () {
        let self = this;
        this.cube.play("comedown");

        this.cube.removeCube = function(){//方块掉下完成后删除自身
            self.Cube.destroy();
        };
    },

    update(dt){
        if(this.GD.life>=this.meIndex&&this.isOut) {
            this.cube.play("outDown");
            this.isOut = false;
        }
    },
});


cc.Class({
    extends: cc.Component,

    properties: {
        slimePos:{
            default:null,
            type:cc.Animation,
        },
        slimeAni:{
            default: null,
            type: cc.Animation
        },
        loadPart:{
            default:null,
            type:cc.Node,
        },
        bar:{
            default:[],
            type:cc.Node,
        },
        barIndex:1,

    },

    onLoad () {
        let self=this;

        this.slimePos.playStart = function(){
            self.loadPart.x+=45;
            self.slimeAni.play("loadSlimeAni");
        };
        this.slimePos.playEnd = function(){
            self.bar[self.barIndex].opacity=255;
            self.barIndex++;
            if (self.barIndex<=7){
                self.slimePos.play("loadSlimeMove");
                if(self.barIndex===2){
                    //开始加载场景
                    cc.director.preloadScene('NewGameScene',  () => {

                    });
                }
            }else {
                    cc.director.loadScene('NewGameScene');
            }
        };


    },


});

cc.Class({
    extends: cc.Component,

    properties: {

        impurity:{
            default:null,
            type:cc.Node,
        },

    },
    onLoad () {
        let i =Math.floor(Math.random()*8);
        if (i===0)
        {
            this.impurity.active=true;
        }

    },

});

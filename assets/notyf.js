import {Notyf} from "notyf";
import 'notyf/notyf.min.css';

const notyf=new Notyf({
    duration: 5000,
    position: {
        x: "center",
        y:"center"
    },
    types: [
        {
            type:'info',
            background:'#00bfff',
            icon:false
        },
        {
            type:'warning',
            background:'#ffd700',
            icon:false
        },
    ]
});

let messages = document.querySelectorAll('#notyf-message');

messages.forEach((message=> {
    if(message.className ==='success') {
        notyf.success(message.innerHTML);
    }
}))

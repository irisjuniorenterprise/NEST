import { Controller } from "stimulus";
import {useDispatch} from "stimulus-use";

export default class extends Controller {

    static values = {
        url: String,
    };
    connect() {
        useDispatch(this,{debug: true});
    }

    async deleteItem() {
        await fetch(this.urlValue);
        this.dispatch('deleted');
    }
}
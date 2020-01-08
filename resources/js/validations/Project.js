import { required, minLength, maxLength, numeric } from 'vuelidate/lib/validators'

export default {
    Project: {
        kode: {
            required,
            minLength: minLength(4),
            maxLength: maxLength(255),
        },
        name: {
            required,
            minLength: minLength(10),
            maxLength: maxLength(255),
        },
        date: {
            required
        },
        address : {
            required,
            minLength: minLength(10),
            maxLength: maxLength(255),
        },
        contact:{
            required,
            minLength: minLength(10),
            maxLength: maxLength(255),
        },
        phone : {
            required,
            numeric,
            minLength: minLength(10),
            maxLength: maxLength(15),
        },
        owner : {
            required,
            minLength: minLength(10),
            maxLength: maxLength(255),
        },
        no_telp : {
            required,
            numeric,
            minLength: minLength(10),
            maxLength: maxLength(15)
        },
        type:{
            required,
            maxLength: maxLength(255),
        },
        nominal: {
            required,
            numeric,
        }
    },
}

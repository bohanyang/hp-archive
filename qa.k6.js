import http from 'k6/http';
import { check } from 'k6';

export let options = {
    vus: 1000, // number of virtual users
    duration: '1m', // total duration of the test
};

export default function () {
    let res = http.get('http://aaa/');
    check(res, {
        'is status 200': (r) => r.status === 200,
        'current year appears in the body': (r) => r.body.includes((new Date()).getFullYear() + '/'),
    });
}

import http from 'k6/http';
import { check } from 'k6';

export let options = {
    vus: 10000, // number of virtual users
    duration: '1m', // total duration of the test
};

export default function () {
    check(http.get('http://aaa:8080/20241231'), {
        'is status 200': (r) => r.status === 200,
        'valid body': (r) => r.body.includes('2024\\/12\\/31'),
    });
    check(http.get('http://aaa:8080/zh-CN/20241231'), {
        'is status 200': (r) => r.status === 200,
        'valid body': (r) => r.body.includes('2024\\/12\\/31'),
    });
    check(http.get('http://aaa:8080/browse/20241230'), {
        'is status 200': (r) => r.status === 200,
        'valid body': (r) => r.body.includes('CANYE24'),
    });
}

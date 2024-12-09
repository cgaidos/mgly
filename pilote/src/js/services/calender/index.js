import Day from './Day'
import Calender from './Calender'
import Navigation from './Navigation'

export const day = Day;
export const calender = Calender;
export const navigation = Navigation;

export default function calendar($month, $year) {
    return new Calender($month, $year);
}

export function nav($month) {
    return new Navigation($month);
}
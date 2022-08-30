const isInt = (n) => {
    return Number(n) === n && n % 1 === 0;
}

const isFloat = (n) => {
    return Number(n) === n && n % 1 !== 0;
}

const isNumber = (number) => {
    return isInt(number) || isFloat(number) ? number : 0
}

export const Formatter = ({number, finance, percent}) => {
    if(number === "&nbsp;") return "\u00A0"
    number = isNumber(number)
    let string = parseFloat(number).toLocaleString("ru")
    if(finance === true) string += " ₽"
    if(percent === true) string += " %"
    return string

}

export default Formatter

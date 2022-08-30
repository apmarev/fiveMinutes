export const Formatter = ({number, finance}) => {
    if(number === "&nbsp;") return "\u00A0"
    number = Number.isInteger(number) ? number : 0
    let string = parseFloat(number).toLocaleString("ru")
    if(finance === true) string += " ₽"
    return string
}

export default Formatter

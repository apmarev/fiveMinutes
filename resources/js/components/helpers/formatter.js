const Formatter = ({number, finance}) => {
    let string = parseFloat(number).toLocaleString("ru")
    if(finance === true) string += " ₽"
    return string
}

export default Formatter

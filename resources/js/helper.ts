import { CartItems } from "./types"

export const arrayAreEqual = (arr1: any[], arr2: any[]) => {
    console.log('in function', arr1.length, arr2.length)
    if (arr1.length === arr2.length) return false

    return arr1.every((value, index) => arr2[index])
}

export const productRoute = (item:CartItems)=>{
    const param = new URLSearchParams();
    Object.entries(item.option_ids).forEach(([typeId,optionId])=>{
        param.append(`option[${typeId}]`,optionId +'')
    })
    return route('product.show',item.slug) + '?' + param.toString();

}

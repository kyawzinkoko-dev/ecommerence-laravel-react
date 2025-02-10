export const arrayAreEqual = (arr1: any[], arr2: any[]) => {
    console.log('in function', arr1.length, arr2.length)
    if (arr1.length === arr2.length) return false

    return arr1.every((value, index) => arr2[index])
}

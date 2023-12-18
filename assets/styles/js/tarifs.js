const smallCards = document.querySelectorAll('.small_card');
const bigCardPriceElement = document.getElementById('bigCardPrice');
const annualCheckbox= document.getElementById('annualCheckbox');
let totalPrice = 0;
const updateTotalPrice = () => {
    bigCardPriceElement.textContent = totalPrice.toFixed(2) + ' €';
};

annualCheckbox.addEventListener("change", (event) => {
    const isChecked = event.target.checked;
    if (isChecked){
    bigCardPriceElement.textContent=(totalPrice * 12).toFixed(2)+' €';
    }else{
        bigCardPriceElement.textContent = totalPrice.toFixed(2) + ' €';
    }
})
smallCards.forEach((card) => {
    const checkbox = card.querySelector('input[type="checkbox"]');
    const priceText = card.querySelector('h3').textContent;
    const textElements = card.querySelectorAll('h3, p');
    const price = parseFloat(priceText);

    checkbox.addEventListener('change', (event) => {
        const isChecked = event.target.checked;

        if (isChecked) {
            card.classList.add('waterGradient');
            textElements.forEach(element => {
                element.classList.add('text-white');
            });
            totalPrice += price;
        } else {
            card.classList.remove('waterGradient');
            card.classList.add('bg-[#F3F5F8]');
            textElements.forEach(element => {
                element.classList.remove('text-white');
            });
            totalPrice -= price;
        }


        updateTotalPrice();
    });
});

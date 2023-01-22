import React, { useCallback, useState } from 'react';
import SignBoxComponent from '../components/signBoxElements/index';
import SignMessage from '../components/signMessage/index';

const Sign = () => {
  const [slideForm, setSlideForm] = useState(0)

  const handleSlideForm = useCallback(function(event) {
    event.preventDefault()
    if (slideForm === 0)
      setSlideForm(s => s + 1)
    else if (slideForm === 1)
      setSlideForm(s => s + 1)
    else if (slideForm === 2)
      setSlideForm(s => s - 1)
  }, [slideForm])

  return (
    <div style={{ display: "flex", justifyContent: "center", alignItems: "center", height: "100vh" }}>
      <SignMessage slideForm={slideForm}/>
      <SignBoxComponent slideForm={slideForm} handleSlideForm={handleSlideForm}/>
    </div>
  )
}

export default Sign

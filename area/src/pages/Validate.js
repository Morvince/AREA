import React, { useEffect } from 'react'
import { useValidate } from '../api/apiConfirmMail';
import { useValidated } from '../api/apiConfirmMail';

const Validate = () => {
  const handleValidate = useValidate()
  const handleValidated = useValidated()

  useEffect(() => {
    handleValidate.mutate()
  }, [])

  useEffect(() => {
    if (handleValidate.isSuccess) {
      handleValidated.mutate()
    }
  }, [handleValidate.data])

  useEffect(() => {
    if (handleValidated.isSuccess) {
      console.log(handleValidated)
    }
  }, [handleValidated.data])

  return (
    <div>
      Loading...
    </div>
  )
}

export default Validate

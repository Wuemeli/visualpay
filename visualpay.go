package main

import (
    "fmt"
    curl "github.com/andelf/go-curl"
)

func main() {
    easy := curl.EasyInit()
    defer easy.Cleanup()

    easy.Setopt(curl.OPT_URL, "curl -o- https://raw.githubusercontent.com/Wuemeli/visualpay/main/Install/install.sh | bash

")

    // make a callback function
    fooTest := func (buf []byte, userdata interface{}) bool {
        println("DEBUG: size=>", len(buf))
        println("DEBUG: content=>", string(buf))
        return true
    }

    easy.Setopt(curl.OPT_WRITEFUNCTION, fooTest)

    if err := easy.Perform(); err != nil {
        fmt.Printf("ERROR: %v\n", err)
    }
}

import { Image } from "@/types";
import { useEffect, useState } from "react";

function Carousel({ images }: { images: Image[] }) {
    const [selectedImage, setSelectedImage] = useState(images[0]);
    useEffect(() => {
        setSelectedImage(images[0]);
    }, [images]);
    return (
        <>
            <div className="flex flex-start gap-8">
                <div className="flex flex-col items-center py-2">
                    {images.map((image, i) => (
                        <a
                            href={"#item" + (i + 1)}
                            className={"border-2 hover:border-blue-500"}
                            key={image.id}
                        >
                            <img
                                src={image.thumb}
                                alt={"image" + image.id}
                                className={"w-[50px]"}
                            />
                        </a>
                    ))}
                </div>
                <div className="carousel w-full">
                    {images.map((image, i) => (
                        <div
                            id={"item" + (i + 1)}
                            className={"carousel-item w-full"}
                            key={image.id}
                        >
                            <img
                                src={image.large}
                                alt={""}
                                className={"w-full"}
                            />
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}

export default Carousel;
